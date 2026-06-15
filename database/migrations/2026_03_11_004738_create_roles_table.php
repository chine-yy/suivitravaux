<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRolesTable extends Migration
{
    /**
     * Run the migrations.
     * Après création de la table roles, on ajoute la contrainte FK de users.role_id
     * (anciennement dans update_users_table)
     */
    public function up()
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nom');
            $table->string('slug')->unique();
            $table->boolean('statut')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // Ajout de la contrainte FK users.role_id → roles.id
        // (users est créé avant roles, la FK ne peut être ajoutée qu'ici)
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
        });

        // Ajout de la contrainte FK admins.role_id → roles.id
        // (admins est créé avant roles, la FK ne peut être ajoutée qu'ici)
        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // Supprimer la FK sur admins
        try {
            if (Schema::hasTable('admins')) {
                Schema::table('admins', function (Blueprint $table) {
                    $table->dropForeign(['role_id']);
                });
            }
        } catch (\Exception $e) {
            // Ignorer
        }

        // Supprimer d'abord la FK sur users
        try {
            if (Schema::hasTable('users')) {
                Schema::table('users', function (Blueprint $table) {
                    $table->dropForeign(['role_id']);
                });
            }
        } catch (\Exception $e) {
            // Ignorer si la FK n'existe pas
        }

        Schema::dropIfExists('roles');
    }
}
