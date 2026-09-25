<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('max_teams')->default(1);
            $table->unsignedInteger('max_players')->default(25);
            $table->json('features');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $now = now();
        DB::table('subscription_plans')->insert([
            [
                'slug' => 'basic',
                'name' => 'Basic',
                'price' => 20,
                'max_teams' => 1,
                'max_players' => 25,
                'features' => json_encode(['club.profile', 'club.basic_information']),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'standard',
                'name' => 'Standard',
                'price' => 50,
                'max_teams' => 5,
                'max_players' => 150,
                'features' => json_encode([
                    'club.profile', 'club.basic_information', 'players', 'teams', 'matches', 'news',
                ]),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'premium',
                'name' => 'Premium',
                'price' => 100,
                'max_teams' => 999,
                'max_players' => 9999,
                'features' => json_encode([
                    'club.profile', 'club.basic_information', 'players', 'teams', 'matches', 'news',
                    'advanced.statistics', 'advanced.management', 'additional.content',
                ]),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro Academy',
                'price' => 50,
                'max_teams' => 5,
                'max_players' => 150,
                'features' => json_encode([
                    'club.profile', 'club.basic_information', 'players', 'teams', 'matches', 'news',
                ]),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'unlimited',
                'name' => 'Unlimited',
                'price' => 100,
                'max_teams' => 999,
                'max_players' => 9999,
                'features' => json_encode([
                    'club.profile', 'club.basic_information', 'players', 'teams', 'matches', 'news',
                    'advanced.statistics', 'advanced.management', 'additional.content',
                ]),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
