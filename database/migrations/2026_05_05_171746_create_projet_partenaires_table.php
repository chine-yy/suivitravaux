<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('projet_clients')) {
            Schema::rename('projet_clients', 'projet_partenaires');
        } elseif (!Schema::hasTable('projet_partenaires')) {
            Schema::create('projet_partenaires', function (Blueprint $table) {
                $table->id();
                $table->foreignId('projet_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->timestamps();
                $table->unique(['projet_id', 'user_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_partenaires');
        Schema::dropIfExists('projet_clients');
    }
};
