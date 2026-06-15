<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rendezvous', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('partenaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('titre');
            $table->text('description')->nullable();
            $table->dateTime('date_heure');
            $table->integer('duree_minutes')->default(60);
            $table->string('lieu')->nullable();
            $table->enum('type', ['reunion', 'visite', 'appel', 'autre'])->default('reunion');
            $table->string('type_autre')->nullable();
            $table->enum('statut', ['planifie', 'confirme', 'termine', 'annule'])->default('planifie');
            $table->boolean('rappel')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rendezvous');
    }
};
