<?php

namespace Tests\Feature;

use App\Models\Academy;
use App\Models\Club;
use App\Models\MatchDay;
use App\Models\Player;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchStatsAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_standard_subscriber_can_list_matches_but_not_match_statistics(): void
    {
        [$user, $match, $player] = $this->createMatchForPlan('standard');
        $match->players()->attach($player->id, [
            'attended' => true,
            'goals' => 2,
            'assists' => 1,
        ]);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/matches')
            ->assertOk()
            ->assertJsonMissingPath('data.0.players');

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/matches/{$match->id}/stats", [
                'status' => 'completed',
                'home_score' => 2,
                'away_score' => 0,
                'players' => [[
                    'id' => $player->id,
                    'attended' => true,
                    'goals' => 2,
                    'assists' => 1,
                ]],
            ])
            ->assertForbidden()
            ->assertJsonPath('feature', 'advanced_stats');
    }

    public function test_premium_subscriber_can_read_and_update_match_statistics(): void
    {
        [$user, $match, $player] = $this->createMatchForPlan('premium');
        $match->players()->attach($player->id);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/matches')
            ->assertOk()
            ->assertJsonPath('data.0.players.0.id', $player->id);

        $this->actingAs($user, 'sanctum')
            ->putJson("/api/matches/{$match->id}/stats", [
                'status' => 'completed',
                'home_score' => 1,
                'away_score' => 0,
                'players' => [[
                    'id' => $player->id,
                    'attended' => true,
                    'goals' => 1,
                    'assists' => 0,
                ]],
            ])
            ->assertOk()
            ->assertJsonPath('data.players.0.id', $player->id);

        $this->assertDatabaseHas('match_day_player', [
            'match_day_id' => $match->id,
            'player_id' => $player->id,
            'goals' => 1,
        ]);
    }

    private function createMatchForPlan(string $plan): array
    {
        $club = Club::create(['name' => 'Match Club', 'status' => 'active']);
        $user = User::factory()->create(['club_id' => $club->id]);
        $features = $plan === 'premium'
            ? ['club_profile', 'players', 'teams', 'matches', 'advanced_stats', 'tactics']
            : ['club_profile', 'players', 'teams', 'matches'];

        Subscription::create([
            'user_id' => $user->id,
            'club_id' => $club->id,
            'plan_type' => $plan,
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => $features,
            'ends_at' => now()->addMonth(),
        ]);

        $academy = Academy::create([
            'name' => 'Match Academy',
            'slug' => 'match-academy-' . $club->id,
        ]);
        $team = Team::create([
            'academy_id' => $academy->id,
            'club_id' => $club->id,
            'name' => 'Match Team',
            'age_group' => 'u19',
        ]);
        $player = Player::create([
            'team_id' => $team->id,
            'club_id' => $club->id,
            'name' => 'Match Player',
            'primary_position' => 'ST',
        ]);
        $match = MatchDay::create([
            'team_id' => $team->id,
            'opponent' => 'Opponent',
            'is_home' => true,
            'scheduled_at' => now()->addDay(),
            'status' => 'scheduled',
        ]);

        return [$user, $match, $player];
    }
}
