<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('interventions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('tache_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('sous_tache_id')->nullable()->constrained('sous_taches')->onDelete('set null');
            $table->unsignedBigInteger('technicien_id')->nullable();
            $table->foreignId('partenaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('type', ['installation', 'maintenance', 'reparation', 'inspection', 'autre'])->default('maintenance');
            $table->string('type_autre')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('date_intervention')->nullable();
            $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
            $table->text('rapport')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interventions');
    }
};
