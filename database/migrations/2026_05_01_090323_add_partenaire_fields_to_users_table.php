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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('projet_id')->nullable()->constrained('projets')->onDelete('set null');
            $table->foreignId('chef_equipe_id')->nullable()->constrained('equipes')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['chef_equipe_id']);
            $table->dropForeign(['projet_id']);
            $table->dropColumn(['chef_equipe_id', 'projet_id']);
        });
    }
};
