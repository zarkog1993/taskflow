<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Player;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\TrainingSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class AnalyticsRsvpTest extends TestCase
{
    use RefreshDatabase;

    public function test_signed_training_rsvp_responses_are_reflected_in_attendance_analytics(): void
    {
        [$user, $team, $players] = $this->createClubWithPlayers();
        $firstSession = $this->createTraining($user, $team, now()->subDays(3));
        $secondSession = $this->createTraining($user, $team, now()->subDays(2));
        $futureSession = $this->createTraining($user, $team, now()->addDay());

        $this->invitePlayers($firstSession, [$players[0], $players[1]]);
        $this->invitePlayers($secondSession, [$players[0], $players[1]]);
        $this->invitePlayers($futureSession, [$players[0]]);

        $this->submitRsvp($firstSession, $players[0], 'accepted')
            ->assertRedirect();
        $this->submitRsvp($firstSession, $players[1], 'declined')
            ->assertRedirect();
        $this->submitRsvp($secondSession, $players[0], 'accepted')
            ->assertRedirect();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/analytics');

        $response
            ->assertOk()
            ->assertJsonPath('summary.total_trainings', 2)
            ->assertJsonPath('summary.avg_attended_players', 1)
            ->assertJsonPath('summary.avg_attendance_rate', 50);

        $analyticsPlayers = collect($response->json('players'))->keyBy('id');

        $this->assertSame(2, $analyticsPlayers[$players[0]->id]['trainings_invited']);
        $this->assertSame(2, $analyticsPlayers[$players[0]->id]['trainings_attended']);
        $this->assertSame(100, $analyticsPlayers[$players[0]->id]['attendance_rate']);
        $this->assertSame(2, $analyticsPlayers[$players[1]->id]['trainings_invited']);
        $this->assertSame(0, $analyticsPlayers[$players[1]->id]['trainings_attended']);
        $this->assertSame(0, $analyticsPlayers[$players[1]->id]['attendance_rate']);
        $this->assertSame(0, $analyticsPlayers[$players[2]->id]['trainings_invited']);
        $this->assertDatabaseHas('event_invitations', [
            'invitable_type' => 'training',
            'invitable_id' => $firstSession->id,
            'player_id' => $players[0]->id,
            'status' => 'accepted',
        ]);
        $this->assertDatabaseHas('event_invitations', [
            'invitable_type' => 'training',
            'invitable_id' => $firstSession->id,
            'player_id' => $players[1]->id,
            'status' => 'declined',
        ]);
    }

    public function test_unsigned_rsvp_link_cannot_change_invitation_status(): void
    {
        [$user, $team, $players] = $this->createClubWithPlayers();
        $session = $this->createTraining($user, $team, now()->subDay());
        $this->invitePlayers($session, [$players[0]]);

        $this->get("/api/invitations/training/{$session->id}/{$players[0]->id}/accepted")
            ->assertForbidden();

        $this->assertDatabaseHas('event_invitations', [
            'invitable_type' => 'training',
            'invitable_id' => $session->id,
            'player_id' => $players[0]->id,
            'status' => 'pending',
            'responded_at' => null,
        ]);
    }

    private function createClubWithPlayers(): array
    {
        $club = Club::create(['name' => 'Analytics RSVP Club', 'status' => 'active']);
        $user = User::factory()->create(['club_id' => $club->id]);
        Subscription::create([
            'user_id' => $user->id,
            'club_id' => $club->id,
            'plan_type' => 'premium',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => ['advanced_stats'],
            'ends_at' => now()->addMonth(),
        ]);

        $team = Team::create([
            'club_id' => $club->id,
            'name' => 'Analytics RSVP Team',
            'age_group' => 'u19',
        ]);
        $players = collect(['Player One', 'Player Two', 'Player Three'])
            ->map(fn (string $name) => Player::create([
                'club_id' => $club->id,
                'team_id' => $team->id,
                'name' => $name,
                'primary_position' => 'CM',
            ]))
            ->all();

        return [$user, $team, $players];
    }

    private function createTraining(User $user, Team $team, $scheduledAt): TrainingSession
    {
        return TrainingSession::create([
            'club_id' => $team->club_id,
            'team_id' => $team->id,
            'created_by' => $user->id,
            'title' => 'Analytics Test Training',
            'type' => 'training',
            'status' => 'planned',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    private function invitePlayers(TrainingSession $session, array $players): void
    {
        $session->invitedPlayers()->attach(collect($players)->mapWithKeys(
            fn (Player $player) => [$player->id => ['status' => 'pending']],
        )->all());
    }

    private function submitRsvp(TrainingSession $session, Player $player, string $status)
    {
        $url = URL::temporarySignedRoute('invitations.rsvp', now()->addDay(), [
            'type' => 'training',
            'event' => $session->id,
            'player' => $player->id,
            'status' => $status,
        ]);

        return $this->get($url);
    }
}
