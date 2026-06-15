<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     * Inclut : name, prenom, email, telephone, role_id, type_compte, is_active
     * (anciennement ajoutés par update_users_table + update_users_add_fields)
     */
    public function up()
    {
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('prenom')->nullable();
                $table->string('email', 191)->unique();
                $table->string('telephone', 20)->nullable();

                // Rôle dynamique (FK vers roles, créé APRÈS la migration des rôles)
                $table->unsignedBigInteger('role_id')->nullable()->index();
                $table->enum('type_compte', ['super_admin', 'admin', 'partenaire', 'role_personnalise'])->default('role_personnalise');
                $table->boolean('is_active')->default(true);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password')->nullable();
                $table->string('photo')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
