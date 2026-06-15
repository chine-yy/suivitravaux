<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('type', ['contrat', 'facture', 'rapport', 'photo', 'plan', 'autre'])->default('autre');
            $table->string('type_personnalise')->nullable();
            $table->string('nom');
            $table->string('fichier')->nullable();
            $table->text('description')->nullable();
            $table->string('categorie')->nullable();
            $table->enum('statut', ['actif', 'archive'])->default('actif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
