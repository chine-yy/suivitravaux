<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntreprisesTable extends Migration
{
    /**
     * Run the migrations.
     * Note: The entreprises table already exists in the database.
     *
     * @return void
     */
    public function up()
    {
        // Check if table already exists (it was created manually before)
        if (!Schema::hasTable('entreprises')) {
            Schema::create('entreprises', function (Blueprint $table) {
               $table->bigIncrements('id');
               $table->string('id_entreprise', 20)->unique();
               $table->string('nom_entreprise');
               $table->string('adresse')->nullable();
               $table->string('telephone')->nullable();
               $table->string('email')->nullable();
               $table->string('site_web')->nullable();
               $table->string('ville')->nullable();
               $table->string('pays')->nullable();
               $table->text('description')->nullable();
               $table->string('industry')->nullable();
               $table->boolean('statut')->default(true);
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('entreprises');
        Schema::enableForeignKeyConstraints();
    }
}

