<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title'); // npr. "Tranzicija po izgubljenoj lopti"
            $table->text('description')->nullable();
            $table->enum('type', ['training', 'match', 'tactical_analysis', 'fitness'])->default('training');
            $table->enum('status', ['planned', 'in_progress', 'completed'])->default('planned');
            $table->dateTime('scheduled_at');
            $table->string('location')->nullable(); // Teren A, Balon, Main Stadium
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
    }
};