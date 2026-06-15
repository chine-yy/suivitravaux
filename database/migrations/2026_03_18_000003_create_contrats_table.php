<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contrats', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('partenaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->string('numero_contrat')->unique();
            $table->enum('type', ['prestation', 'marche', 'sous_traitance', 'autre'])->default('prestation');
            $table->text('objet')->nullable();
            $table->decimal('montant', 15, 2)->default(0);
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->text('conditions')->nullable();
            $table->enum('statut', ['brouillon', 'signe', 'en_cours', 'termine', 'annule'])->default('brouillon');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contrats');
    }
};
