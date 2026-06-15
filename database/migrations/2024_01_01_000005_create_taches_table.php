<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('taches')) {
            Schema::create('taches', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('projet_id')->constrained()->onDelete('cascade');
                $table->foreignId('phase_id')->nullable()->constrained()->onDelete('set null');
                $table->string('titre');
                $table->text('description')->nullable();
                $table->enum('priorite', ['basse', 'normale', 'haute', 'critique'])->default('normale');
                $table->enum('statut', ['a_faire', 'en_cours', 'terminee', 'bloquee'])->default('a_faire');
                $table->integer('avancement')->default(0);
                $table->date('date_debut_prevue')->nullable();
                $table->date('date_fin_prevue')->nullable();
                $table->date('date_debut_reelle')->nullable();
                $table->date('date_fin_reelle')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('taches');
    }
}
