<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academies', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // npr. "FK Rudar Akademija"
            $table->string('slug')->unique(); // "fk-rudar"
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        // Veza korisnika sa akademijom
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('academy_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['academy_id']);
            $table->dropColumn('academy_id');
        });
        Schema::dropIfExists('academies');
    }
};
