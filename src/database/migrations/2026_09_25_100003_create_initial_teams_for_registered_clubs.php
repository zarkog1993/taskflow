<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $academyId = DB::table('academies')->value('id');

        if (!$academyId) {
            $academyId = DB::table('academies')->insertGetId([
                'name' => 'Glavna Akademija',
                'slug' => 'default-academy',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('clubs')
            ->whereNotExists(function ($query) {
                $query->selectRaw('1')
                    ->from('teams')
                    ->whereColumn('teams.club_id', 'clubs.id');
            })
            ->orderBy('id')
            ->each(function ($club) use ($academyId) {
                $teamId = DB::table('teams')->insertGetId([
                    'academy_id' => $academyId,
                    'club_id' => $club->id,
                    'name' => $club->name,
                    'age_group' => 'senior',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $ownerId = DB::table('users')
                    ->where('club_id', $club->id)
                    ->orderBy('id')
                    ->value('id');

                if ($ownerId) {
                    DB::table('team_user')->insertOrIgnore([
                        'team_id' => $teamId,
                        'user_id' => $ownerId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Existing club teams may contain user data, so this data migration is intentionally irreversible.
    }
};
