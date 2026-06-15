<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBudgetProjetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('budget_projets')) {
            Schema::create('budget_projets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('budget_id')->constrained()->onDelete('cascade');
                $table->foreignId('projet_id')->constrained()->onDelete('cascade');
                $table->decimal('montant_alloue', 15, 2);
                $table->decimal('montant_consomme', 15, 2)->default(0);
                $table->timestamps();

                $table->unique(['budget_id', 'projet_id']);
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
        Schema::dropIfExists('budget_projets');
    }
}
