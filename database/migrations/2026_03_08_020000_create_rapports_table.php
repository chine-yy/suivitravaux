<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRapportsTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('rapports');

        Schema::create('rapports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('projet_id')->nullable()->index();
            $table->foreign('projet_id')->nullable()->references('id')->on('projets')->onDelete('cascade');
            $table->foreignId('auteur_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('sous_tache_id')->nullable()->index();
            $table->foreign('sous_tache_id')->nullable()->references('id')->on('sous_taches')->onDelete('cascade');
            $table->enum('type', ['journalier', 'hebdomadaire', 'mensuel', 'incident', 'fin_tache', 'sous_tache']);
            $table->string('titre');
            $table->text('contenu')->nullable();
            $table->text('observations')->nullable();
            $table->text('difficultes')->nullable();
            $table->text('solutions')->nullable();
            $table->enum('statut', ['soumis', 'en_revision', 'valide', 'rejete'])->default('soumis');
            $table->integer('avancement_constate')->nullable();
            $table->string('destinataire')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rapports');
    }
}
