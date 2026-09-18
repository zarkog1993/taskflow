<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained('training_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('attended')->default(false);
            $table->timestamps();

            // Sprečava dupliranje istog igrača na istom treningu
            $table->unique(['training_session_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_user');
    }
};
