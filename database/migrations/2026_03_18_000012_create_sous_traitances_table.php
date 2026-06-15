<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sous_traitances', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->string('nom_entreprise');
            $table->string('contact_nom')->nullable();
            $table->string('contact_prenom')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_telephone')->nullable();
            $table->text('description_tache')->nullable();
            $table->integer('nombre_employes')->default(1);
            $table->decimal('montant_contrat', 15, 2)->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();
            $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'annule'])->default('en_attente');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sous_traitances');
    }
};
