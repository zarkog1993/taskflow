<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('team_tactics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('formation', 10);
            $table->json('positions');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('team_tactics');
    }
};
