<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetsTable extends Migration
{
    /**
     * Run the migrations.
     * Inclut : les colonnes de base.
     */
    public function up()
    {
        if (!Schema::hasTable('budgets')) {
            Schema::create('budgets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->integer('annee');
                $table->decimal('budget_total', 65, 2);
                $table->text('description')->nullable();
                $table->enum('statut', ['brouillon', 'valide', 'clos'])->default('brouillon');
                $table->timestamps();

            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('budgets');
    }
}
