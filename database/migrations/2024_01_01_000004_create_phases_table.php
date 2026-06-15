<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('phases')) {
            Schema::create('phases', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('projet_id')->constrained()->onDelete('cascade');
                $table->string('nom');
                $table->text('description')->nullable();
                $table->integer('ordre')->default(1);
                $table->date('date_debut')->nullable();
                $table->date('date_fin_prevue')->nullable();
                $table->date('date_fin_reelle')->nullable();
                $table->integer('avancement')->default(0);
                $table->enum('statut', ['en_attente', 'en_cours', 'terminee', 'bloquee'])->default('en_attente');
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
        Schema::dropIfExists('phases');
    }
}
