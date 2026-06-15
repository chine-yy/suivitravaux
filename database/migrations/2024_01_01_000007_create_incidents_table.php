<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            $table->foreignId('phase_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('signale_par')->nullable()->constrained('users')->onDelete('set null');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->enum('gravite', ['faible', 'moyen', 'critique'])->default('moyen');
            $table->enum('statut', ['ouvert', 'en_traitement', 'resolu', 'ferme'])->default('ouvert');
            $table->text('resolution')->nullable();
            $table->foreignId('resolu_par')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('date_resolution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('incidents');
    }
}

