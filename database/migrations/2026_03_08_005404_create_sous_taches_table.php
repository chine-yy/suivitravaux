<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSousTachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('sous_taches')) {
            Schema::create('sous_taches', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('tache_id')->nullable()->constrained()->onDelete('cascade');
                $table->string('titre');
                $table->text('description')->nullable();
                $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'bloquee'])->default('en_attente');
                $table->date('date_debut')->nullable();
                $table->date('date_fin_prevue')->nullable();
                $table->integer('avancement')->default(0);
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
        Schema::dropIfExists('sous_taches');
    }
}
