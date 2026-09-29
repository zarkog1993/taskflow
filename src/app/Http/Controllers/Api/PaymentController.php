<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\PlayerPayment;
use App\Models\Team;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    private const string SENIOR_AGE_GROUP = 'senior';

    /**
     * Objedinjeni finansijski pregled za ceo klub (ili jednu ekipu) za zadati period.
     * Vraća red po igraču i onda kada uplata još uvek ne postoji u bazi.
     */
    public function overview(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'nullable|integer|exists:teams,id',
            'period' => 'nullable|string|size:7|regex:/^\d{4}-\d{2}$/',
            'type' => 'nullable|in:membership,stipend',
        ]);

        $period = $validated['period'] ?? now()->format('Y-m');
        $type = $validated['type'] ?? 'membership';

        $teams = $this->authorizedTeams($request);

        if (!empty($validated['team_id'])) {
            $teams = $teams->where('id', (int) $validated['team_id'])->values();

            abort_if($teams->isEmpty(), 403, 'Nemate pristup odabranoj ekipi.');
        } else {
            // Bez izabrane ekipe: članarine se odnose na akademiju, honorari na seniore.
            $teams = $teams->filter(fn (Team $team) => $type === 'stipend'
                ? $team->age_group === self::SENIOR_AGE_GROUP
                : $team->age_group !== self::SENIOR_AGE_GROUP)->values();
        }

        $teamIds = $teams->pluck('id');

        $players = Player::whereIn('team_id', $teamIds)
            ->orderBy('team_id')
            ->orderBy('name')
            ->get();

        $payments = PlayerPayment::whereIn('team_id', $teamIds)
            ->where('period', $period)
            ->where('type', $type)
            ->get()
            ->keyBy('player_id');

        $teamNames = $teams->pluck('name', 'id');

        $rows = $players->map(function (Player $player) use ($payments, $teamNames) {
            $payment = $payments->get($player->id);

            return [
                'player_id' => $player->id,
                'player_name' => $player->name,
                'jersey_number' => $player->jersey_number,
                'position' => $player->primary_position,
                'team_id' => $player->team_id,
                'team_name' => $teamNames[$player->team_id] ?? null,
                'payment_id' => $payment?->id,
                'has_payment' => $payment !== null,
                // null znači "još nije evidentirano" — frontend tada koristi podrazumevani mesečni iznos.
                'amount' => $payment ? (float) $payment->amount : null,
                'status' => $payment->status ?? 'pending',
                'paid_at' => $payment?->paid_at?->toIso8601String(),
                'note' => $payment->note ?? null,
            ];
        })->values();

        return response()->json([
            'data' => [
                'period' => $period,
                'type' => $type,
                'summary' => $this->summarize($rows),
                'rows' => $rows,
            ],
        ]);
    }

    /**
     * Vraća evidentirane uplate za određenu ekipu i period.
     */
    public function index(Request $request, Team $team): JsonResponse
    {
        $this->authorizeTeam($request, $team);

        $period = $request->query('period', now()->format('Y-m'));

        $payments = PlayerPayment::where('team_id', $team->id)
            ->where('period', $period)
            ->when(
                $request->query('type'),
                fn ($query, $type) => $query->where('type', $type),
            )
            ->with('player')
            ->get();

        return response()->json([
            'period' => $period,
            'payments' => $payments,
        ]);
    }

    /**
     * Evidentira ili ažurira status uplate/isplate za igrača.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|integer|exists:teams,id',
            'player_id' => 'required|integer|exists:players,id',
            'type' => 'required|in:membership,stipend',
            'period' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
            'amount' => 'required|numeric|min:0|max:99999999.99',
            'status' => 'required|in:paid,pending,overdue',
            'note' => 'nullable|string|max:255',
        ]);

        $team = Team::findOrFail($validated['team_id']);
        $this->authorizeTeam($request, $team);

        // Globalni scope na Player modelu dodatno izoluje igrače po klubu.
        $player = Player::where('id', $validated['player_id'])
            ->where('team_id', $team->id)
            ->firstOrFail();

        $payment = PlayerPayment::updateOrCreate(
            [
                'team_id' => $team->id,
                'player_id' => $player->id,
                'type' => $validated['type'],
                'period' => $validated['period'],
            ],
            [
                'club_id' => $team->club_id,
                'amount' => $validated['amount'],
                'status' => $validated['status'],
                'paid_at' => $validated['status'] === 'paid' ? now() : null,
                'note' => $validated['note'] ?? null,
            ],
        );

        return response()->json([
            'message' => 'Status finansija uspešno ažuriran.',
            'data' => $payment->fresh(),
        ]);
    }

    /**
     * Masovni upsert — npr. primena istog mesečnog iznosa na ceo spisak igrača.
     */
    public function bulkStoreOrUpdate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:membership,stipend',
            'period' => 'required|string|size:7|regex:/^\d{4}-\d{2}$/',
            'items' => 'required|array|min:1|max:500',
            'items.*.team_id' => 'required|integer|exists:teams,id',
            'items.*.player_id' => 'required|integer|exists:players,id',
            'items.*.amount' => 'required|numeric|min:0|max:99999999.99',
            'items.*.status' => 'required|in:paid,pending,overdue',
            'items.*.note' => 'nullable|string|max:255',
        ]);

        $teams = Team::whereIn('id', collect($validated['items'])->pluck('team_id')->unique())
            ->get()
            ->keyBy('id');

        foreach ($teams as $team) {
            $this->authorizeTeam($request, $team);
        }

        $payments = DB::transaction(function () use ($validated, $teams) {
            return collect($validated['items'])->map(function (array $item) use ($validated, $teams) {
                $team = $teams[$item['team_id']];

                $player = Player::where('id', $item['player_id'])
                    ->where('team_id', $team->id)
                    ->firstOrFail();

                return PlayerPayment::updateOrCreate(
                    [
                        'team_id' => $team->id,
                        'player_id' => $player->id,
                        'type' => $validated['type'],
                        'period' => $validated['period'],
                    ],
                    [
                        'club_id' => $team->club_id,
                        'amount' => $item['amount'],
                        'status' => $item['status'],
                        'paid_at' => $item['status'] === 'paid' ? now() : null,
                        'note' => $item['note'] ?? null,
                    ],
                );
            });
        });

        return response()->json([
            'message' => 'Iznosi su uspešno primenjeni na ' . $payments->count() . ' igrača.',
            'data' => $payments,
        ]);
    }

    private function authorizedTeams(Request $request): Collection
    {
        return Team::query()
            ->when(
                !$request->user()->isSuperAdmin(),
                fn ($query) => $query->where('club_id', $request->user()->club_id),
            )
            ->orderBy('name')
            ->get();
    }

    private function authorizeTeam(Request $request, Team $team): void
    {
        abort_unless(
            $request->user()->isSuperAdmin()
            || (int) $team->club_id === (int) $request->user()->club_id,
            403,
        );
    }

    private function summarize(Collection $rows): array
    {
        return [
            'expected' => round((float) $rows->sum('amount'), 2),
            'collected' => round((float) $rows->where('status', 'paid')->sum('amount'), 2),
            'pending' => round((float) $rows->where('status', 'pending')->sum('amount'), 2),
            'overdue' => round((float) $rows->where('status', 'overdue')->sum('amount'), 2),
            'players_count' => $rows->count(),
            'paid_count' => $rows->where('status', 'paid')->count(),
        ];
    }
}
