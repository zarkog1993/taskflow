<?php

namespace Tests\Feature;

use App\Mail\ClubOnboardingMail;
use App\Models\Club;
use App\Models\Player;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ClubTeamAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_and_associates_initial_club_team(): void
    {
        Mail::fake();
        Role::create(['name' => 'Club Admin', 'slug' => 'club-admin']);

        $response = $this->postJson('/api/register', [
            'name' => 'Club Owner',
            'club_name' => 'FK Test',
            'email' => 'owner@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertCreated();

        $user = User::where('email', 'owner@example.com')->firstOrFail();
        $team = Team::where('club_id', $user->club_id)->firstOrFail();

        $this->assertSame('FK Test', $user->club->name);
        $this->assertSame('FK Test', $team->name);
        $this->assertSame(['club-admin'], $user->roles()->pluck('slug')->all());
        $this->assertTrue($team->members()->whereKey($user->id)->exists());
        Mail::assertSent(ClubOnboardingMail::class, fn ($mail) => $mail->hasTo($user->email));
    }

    public function test_player_cannot_create_another_player(): void
    {
        $club = Club::create(['name' => 'Player Club', 'status' => 'active']);
        $team = Team::create([
            'club_id' => $club->id,
            'name' => 'Player Club',
            'age_group' => 'senior',
        ]);
        $playerUser = User::factory()->create(['club_id' => $club->id]);
        $playerRole = Role::firstOrCreate(['slug' => 'player'], ['name' => 'Player']);
        $playerUser->roles()->sync([$playerRole->id]);
        Subscription::create([
            'user_id' => $playerUser->id,
            'club_id' => $club->id,
            'plan_type' => 'standard',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => ['players'],
        ]);

        $this->actingAs($playerUser, 'sanctum')->postJson('/api/players', [
            'team_id' => $team->id,
            'name' => 'New Player',
            'primary_position' => 'CM',
        ])->assertForbidden();

        $this->assertDatabaseMissing('players', ['name' => 'New Player']);
    }

    public function test_club_admin_only_sees_teams_from_their_club(): void
    {
        [$owner, $ownTeam] = $this->createClubOwner('Own Club');
        [, $otherTeam] = $this->createClubOwner('Other Club');

        $response = $this->actingAs($owner, 'sanctum')->getJson('/api/teams');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $ownTeam->id)
            ->assertJsonMissing(['id' => $otherTeam->id]);
    }

    public function test_club_admin_cannot_assign_player_to_another_clubs_team(): void
    {
        [$owner, $ownTeam] = $this->createClubOwner('Own Club');
        [, $otherTeam] = $this->createClubOwner('Other Club');
        $player = Player::create([
            'club_id' => $ownTeam->club_id,
            'team_id' => $ownTeam->id,
            'name' => 'Player',
            'primary_position' => 'CM',
        ]);

        $this->actingAs($owner, 'sanctum')
            ->putJson("/api/players/{$player->id}", ['team_id' => $otherTeam->id])
            ->assertNotFound();
    }

    public function test_club_admin_cannot_schedule_training_or_match_for_another_club(): void
    {
        [$owner] = $this->createClubOwner('Own Club');
        [, $otherTeam] = $this->createClubOwner('Other Club');

        $this->actingAs($owner, 'sanctum')->postJson('/api/training-sessions', [
            'team_id' => $otherTeam->id,
            'title' => 'Unauthorized training',
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ])->assertForbidden();

        $this->actingAs($owner, 'sanctum')->postJson('/api/matches', [
            'team_id' => $otherTeam->id,
            'opponent' => 'Other Team',
            'is_home' => true,
            'scheduled_at' => now()->addDay()->toDateTimeString(),
        ])->assertForbidden();
    }

    private function createClubOwner(string $clubName): array
    {
        $club = Club::create(['name' => $clubName, 'status' => 'active']);
        $owner = User::factory()->create(['club_id' => $club->id]);
        $role = Role::firstOrCreate(['slug' => 'club-admin'], ['name' => 'Club Admin']);
        $owner->roles()->syncWithoutDetaching([$role->id]);

        $team = Team::create([
            'club_id' => $club->id,
            'name' => $clubName,
            'age_group' => 'senior',
        ]);
        $team->members()->syncWithoutDetaching([$owner->id]);

        Subscription::create([
            'user_id' => $owner->id,
            'club_id' => $club->id,
            'plan_type' => 'standard',
            'status' => 'active',
            'max_teams' => 5,
            'max_players' => 150,
            'features' => ['teams', 'players', 'matches'],
        ]);

        return [$owner, $team];
    }
}
