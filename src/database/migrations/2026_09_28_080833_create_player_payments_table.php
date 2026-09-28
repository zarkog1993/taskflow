<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            // Tip finansije: 'membership' (članarina za akademiju) ili 'stipend' (isplata prvotimcu)
            $table->enum('type', ['membership', 'stipend'])->default('membership');
            // Mesec i godina na koje se odnosi uplata (npr. '2026-09')
            $table->string('period', 7); 
            $table->decimal('amount', 10, 2);
            $table->enum('status', ['paid', 'pending', 'overdue'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_payments');
    }
};
