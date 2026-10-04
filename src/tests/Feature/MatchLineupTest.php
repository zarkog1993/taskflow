<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\MatchDay;
use App\Models\Player;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchLineupTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_admin_can_save_a_proposed_lineup_without_changing_the_match_report(): void
    {
        [$user, $team, $match, $acceptedPlayers] = $this->createMatchWithSubscription(['tactics']);
        $positions = $this->positions($acceptedPlayers[0]->id);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/matches/{$match->id}/lineup")
            ->assertOk()
            ->assertJsonPath('data.has_saved_lineup', false)
            ->assertJsonPath('data.confirmed_players.0.id', $acceptedPlayers[0]->id)
            ->assertJsonPath('data.lineup.formation', '4-3-3');

        $this->putJson("/api/matches/{$match->id}/lineup", [
            'formation' => '4-2-3-1',
            'positions' => $positions,
            'bench_player_ids' => [$acceptedPlayers[1]->id],
        ])
            ->assertOk()
            ->assertJsonPath('data.formation', '4-2-3-1')
            ->assertJsonPath('data.bench_player_ids.0', $acceptedPlayers[1]->id);

        $this->assertDatabaseCount('match_lineups', 1);
        $this->assertDatabaseCount('match_day_player', 0);

        $this->getJson("/api/matches/{$match->id}/lineup")
            ->assertOk()
            ->assertJsonPath('data.has_saved_lineup', true)
            ->assertJsonPath('data.lineup.formation', '4-2-3-1')
            ->assertJsonPath('data.lineup.bench_player_ids.0', $acceptedPlayers[1]->id);
    }

    public function test_only_players_who_accepted_the_invitation_can_be_in_lineup_or_bench(): void
    {
        [$user, , $match, $players] = $this->createMatchWithSubscription(['tactics']);
        $match->invitedPlayers()->updateExistingPivot($players[0]->id, ['status' => 'pending']);
        $positions = $this->positions($players[0]->id);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/matches/{$match->id}/lineup", [
                'formation' => '4-3-3',
                'positions' => $positions,
                'bench_player_ids' => [],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('positions');
    }

    public function test_player_cannot_be_selected_as_starter_and_bench(): void
    {
        [$user, , $match, $players] = $this->createMatchWithSubscription(['tactics']);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/matches/{$match->id}/lineup", [
                'formation' => '4-3-3',
                'positions' => $this->positions($players[0]->id),
                'bench_player_ids' => [$players[0]->id],
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('positions');
    }

    public function test_lineup_is_restricted_to_the_users_club_and_tactics_feature(): void
    {
        [$user] = $this->createMatchWithSubscription(['tactics']);
        [, , $otherMatch] = $this->createMatchWithSubscription(['tactics']);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/matches/{$otherMatch->id}/lineup")
            ->assertForbidden();

        [$userWithoutFeature, , $matchWithoutFeature] = $this->createMatchWithSubscription(['matches']);
        $this->actingAs($userWithoutFeature, 'sanctum')
            ->getJson("/api/matches/{$matchWithoutFeature->id}/lineup")
            ->assertForbidden()
            ->assertJsonPath('feature', 'tactics');
    }

    public function test_lineup_planner_rejects_completed_matches(): void
    {
        [$user, , $match] = $this->createMatchWithSubscription(['tactics']);
        $match->update(['status' => 'completed']);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/matches/{$match->id}/lineup")
            ->assertStatus(409);
    }

    private function createMatchWithSubscription(array $features): array
    {
        $club = Club::create([
            'name' => 'Lineup Club ' . fake()->unique()->numberBetween(1, 99999),
            'status' => 'active',
        ]);
        $user = User::factory()->create(['club_id' => $club->id]);
        $role = Role::firstOrCreate(['slug' => 'club-admin'], ['name' => 'Club Admin']);
        $user->roles()->syncWithoutDetaching([$role->id]);
        Subscription::create([
            'user_id' => $user->id,
            'club_id' => $club->id,
            'plan_type' => 'premium',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => $features,
            'ends_at' => now()->addMonth(),
        ]);

        $team = Team::create([
            'club_id' => $club->id,
            'name' => 'Lineup Team',
            'age_group' => 'u19',
        ]);
        $match = MatchDay::create([
            'team_id' => $team->id,
            'opponent' => 'Opponent',
            'is_home' => true,
            'scheduled_at' => now()->addDay(),
            'status' => 'scheduled',
        ]);
        $players = collect(['Accepted Starter', 'Accepted Reserve', 'Pending Player'])
            ->map(fn (string $name) => Player::create([
                'club_id' => $club->id,
                'team_id' => $team->id,
                'name' => $name,
                'primary_position' => 'CM',
            ]))
            ->all();
        $this->invite($match, $players[0], 'accepted');
        $this->invite($match, $players[1], 'accepted');

        return [$user, $team, $match, $players];
    }

    private function invite(MatchDay $match, Player $player, string $status): void
    {
        $match->invitedPlayers()->attach($player->id, ['status' => $status]);
    }

    private function positions(?int $firstPlayerId = null): array
    {
        return array_map(
            fn (int $spotId) => [
                'spot_id' => $spotId,
                'x' => 50,
                'y' => 50,
                'player_id' => $spotId === 1 ? $firstPlayerId : null,
            ],
            range(1, 11),
        );
    }
}
