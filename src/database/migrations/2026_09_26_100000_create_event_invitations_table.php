<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pozivnice igrača na događaje (trening / utakmica) sa njihovim RSVP odgovorom
        Schema::create('event_invitations', function (Blueprint $table) {
            $table->id();
            $table->morphs('invitable');
            $table->foreignId('player_id')->constrained('players')->cascadeOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined'])->default('pending');
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->unique(['invitable_type', 'invitable_id', 'player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_invitations');
    }
};
