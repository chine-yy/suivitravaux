<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipesTable extends Migration
{
    /**
     * Run the migrations.
     * Inclut : statut, role_id
     * (anciennement ajoutés par add_status_to_equipes_table + add_role_id_to_equipes_table)
     */
    public function up()
    {
        Schema::create('equipes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nom');
            $table->text('description')->nullable();
            $table->enum('statut', ['active', 'inactive', 'suspended'])->default('active');
            $table->foreignId('projet_id')->constrained()->onDelete('cascade');
            // role_id ajouté ici directement (clé étrangère après création de la table roles)
            $table->foreignId('role_id')->nullable()->constrained('roles')->onDelete('cascade');
            $table->foreignId('chef_equipe_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('equipes');
    }
}
