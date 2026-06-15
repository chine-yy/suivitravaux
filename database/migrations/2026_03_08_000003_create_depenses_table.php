<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('depenses')) {
            Schema::create('depenses', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('budget_projet_id')->nullable()->index();
                $table->foreign('budget_projet_id')->nullable()->references('id')->on('budget_projets')->onDelete('cascade');
                $table->unsignedBigInteger('projet_id')->nullable()->index();
                $table->foreign('projet_id')->nullable()->references('id')->on('projets')->onDelete('cascade');
                $table->decimal('montant', 15, 2);
                $table->text('description')->nullable();
                $table->enum('categorie', [
                    'materiaux',
                    'main_oeuvre',
                    'equipement',
                    'transport',
                    'sous_traitance',
                    'services',
                    'autres'
                ])->default('autres');
                $table->date('date_depense');
                $table->enum('type_paiement', [
                    'especes',
                    'virement',
                    'cheque',
                    'carte_bancaire',
                    'autres'
                ])->default('virement');
                $table->string('reference')->nullable();
                $table->enum('statut', ['en_attente', 'validee', 'rejetee'])->default('en_attente');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
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
        Schema::dropIfExists('depenses');
    }
}
