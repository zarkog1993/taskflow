<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabela za pakete/pretplate
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Vlasnik pretplate
            $table->string('stripe_id')->nullable(); // ili Laravel Cashier/Paddle ID
            $table->string('plan_type'); // 'basic', 'pro', 'unlimited'
            $table->string('status'); // 'active', 'canceled', 'past_due'
            $table->integer('max_teams')->default(1);
            $table->integer('max_players')->default(25);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
