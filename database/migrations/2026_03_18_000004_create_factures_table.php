<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('factures', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('partenaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('contrat_id')->nullable()->constrained()->onDelete('set null');
            $table->string('numero_facture')->unique();
            $table->enum('type', ['facture', 'avoir', 'proforma'])->default('facture');
            $table->decimal('montant_ht', 15, 2)->default(0);
            $table->decimal('montant_tva', 15, 2)->default(0);
            $table->decimal('montant_ttc', 15, 2)->default(0);
            $table->date('date_emission')->nullable();
            $table->date('date_echeance')->nullable();
            $table->enum('statut_paiement', ['en_attente', 'paye', 'en_retard', 'annule'])->default('en_attente');
            $table->enum('mode_paiement', ['virement', 'cheque', 'especes', 'carte'])->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('factures');
    }
};
