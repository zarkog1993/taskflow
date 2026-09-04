<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('jersey_number')->nullable();
            $table->string('preferred_foot')->default('right'); // right, left, both
            $table->string('primary_position')->default('CM'); // GK, CB, LB, RB, DM, CM, AM, LW, RW, ST
            $table->date('date_of_birth')->nullable();
            $table->enum('fitness_status', ['fit', 'injured', 'rehab', 'absent'])->default('fit');
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_profiles');
    }
};
