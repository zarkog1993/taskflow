<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin']
        );

        Role::firstOrCreate(
            ['slug' => 'club-admin'],
            ['name' => 'Club Admin']
        );

        Role::firstOrCreate(
            ['slug' => 'player'],
            ['name' => 'Player']
        );
    }
}