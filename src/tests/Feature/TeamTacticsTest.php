<?php

namespace Tests\Feature;

use App\Models\Club;
use App\Models\Player;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamTacticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_club_admin_can_save_and_reload_one_tactic_per_team(): void
    {
        [$user, $team, $player] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        $positions = $this->positions($player->id);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/teams/{$team->id}/tactics")
            ->assertOk()
            ->assertJsonPath('data', null);

        $this->putJson("/api/teams/{$team->id}/tactics", [
            'formation' => '4-2-3-1',
            'positions' => $positions,
        ])
            ->assertOk()
            ->assertJsonPath('data.formation', '4-2-3-1')
            ->assertJsonPath('data.positions.0.player_id', $player->id);

        $this->putJson("/api/teams/{$team->id}/tactics", [
            'formation' => '4-4-2',
            'positions' => $positions,
        ])->assertOk();

        $this->assertDatabaseCount('team_tactics', 1);

        $this->getJson("/api/teams/{$team->id}/tactics")
            ->assertOk()
            ->assertJsonPath('data.formation', '4-4-2')
            ->assertJsonPath('data.positions.0.player_id', $player->id);
    }

    public function test_tactics_cannot_be_saved_with_player_from_another_team(): void
    {
        [$user, $team] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        [, , $otherPlayer] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        $positions = $this->positions();
        $positions[0]['player_id'] = $otherPlayer->id;

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/teams/{$team->id}/tactics", [
                'formation' => '4-3-3',
                'positions' => $positions,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('positions.0.player_id');
    }

    public function test_tactics_are_limited_to_teams_in_the_users_club(): void
    {
        [$user] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        [, $otherTeam] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/teams/{$otherTeam->id}/tactics")
            ->assertForbidden();
    }

    public function test_tactics_require_the_subscription_feature(): void
    {
        [$user, $team] = $this->createTeamWithSubscription(['teams', 'players']);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/teams/{$team->id}/tactics")
            ->assertForbidden()
            ->assertJsonPath('feature', 'tactics');
    }

    public function test_tactics_reject_duplicate_positions_and_out_of_bounds_coordinates(): void
    {
        [$user, $team] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        $positions = $this->positions();
        $positions[1]['spot_id'] = $positions[0]['spot_id'];
        $positions[1]['x'] = 101;

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/teams/{$team->id}/tactics", [
                'formation' => '4-3-3',
                'positions' => $positions,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['positions.1.spot_id', 'positions.1.x']);
    }

    public function test_a_player_cannot_be_assigned_to_multiple_positions(): void
    {
        [$user, $team, $player] = $this->createTeamWithSubscription(['teams', 'players', 'tactics']);
        $positions = $this->positions($player->id);
        $positions[1]['player_id'] = $player->id;

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/teams/{$team->id}/tactics", [
                'formation' => '4-3-3',
                'positions' => $positions,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('positions');
    }

    private function createTeamWithSubscription(array $features): array
    {
        $club = Club::create(['name' => 'Tactics Club ' . fake()->unique()->numberBetween(1, 99999), 'status' => 'active']);
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
            'name' => 'Tactics Team',
            'age_group' => 'u19',
        ]);
        $player = Player::create([
            'club_id' => $club->id,
            'team_id' => $team->id,
            'name' => 'Tactics Player',
            'primary_position' => 'ST',
        ]);

        return [$user, $team, $player];
    }

    private function positions(?int $playerId = null): array
    {
        return array_map(
            fn (int $spotId) => [
                'spot_id' => $spotId,
                'x' => 50,
                'y' => 50,
                'player_id' => $spotId === 1 ? $playerId : null,
            ],
            range(1, 11),
        );
    }
}
