<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjetsTable extends Migration
{
    /**
     * Run the migrations.
     * Inclut : les colonnes de base.
     */
    public function up()
    {
        if (!Schema::hasTable('projets')) {
            Schema::create('projets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nom');
                $table->text('description')->nullable();

                $table->decimal('budget', 15, 2)->nullable();
                $table->decimal('pourcentage_role_principal', 5, 2)->default(10);
                $table->decimal('pourcentage_role_secondaire', 5, 2)->default(5);
                $table->decimal('budget_consomme', 15, 2)->default(0);
                $table->date('date_debut')->nullable();
                $table->date('date_fin_prevue')->nullable();
                $table->date('date_fin_reelle')->nullable();
                $table->integer('avancement')->default(0);
                $table->enum('statut', ['en_attente', 'en_cours', 'en_pause', 'termine', 'en_retard'])->default('en_attente');
                $table->enum('type_travaux', ['construction', 'renovation', 'maintenance', 'installation', 'autre'])->default('construction');
                $table->string('partenaire')->nullable();
                $table->unsignedBigInteger('partenaire_id')->nullable()->index();
                $table->foreign('partenaire_id')->references('id')->on('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('projets');
    }
}
