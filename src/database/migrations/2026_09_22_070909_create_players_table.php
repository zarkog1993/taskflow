<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('primary_position', 10);
            $table->string('seniority', 50)->default('Seniori');
            $table->integer('jersey_number')->nullable();
            $table->integer('height')->nullable(); // u cm
            $table->integer('weight')->nullable(); // u kg
            $table->date('date_of_birth')->nullable();
            $table->string('preferred_foot', 20)->default('right');
            $table->string('physical_status')->default('fit');
            $table->text('medical_notes')->nullable();
            $table->text('coach_notes')->nullable(); // Trenerske beleške

            // Statistika
            $table->integer('matches_played')->default(0);
            $table->integer('trainings_attended')->default(0);
            $table->integer('goals')->default(0);
            $table->integer('assists')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};