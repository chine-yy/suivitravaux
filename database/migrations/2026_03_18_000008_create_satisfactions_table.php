<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('satisfactions', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->foreignId('partenaire_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('projet_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('note')->unsigned();
            $table->text('commentaire')->nullable();
            $table->dateTime('date_envoi')->nullable();
            $table->dateTime('date_reponse')->nullable();
            $table->enum('statut', ['envoye', 'repondu', 'expire'])->default('envoye');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satisfactions');
    }
};
