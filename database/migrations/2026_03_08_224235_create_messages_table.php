<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('sender_id');
            $table->string('sender_type'); // 'Membre', 'ChefEquipe', 'ChefProjet', 'SuperAdmin'
            $table->unsignedBigInteger('receiver_id');
            $table->string('receiver_type'); // 'Membre', 'ChefEquipe', 'ChefProjet', 'SuperAdmin'
            $table->text('message')->nullable();
            $table->string('image_path')->nullable();
            $table->string('audio_path')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamps();

            $table->index(['sender_id', 'sender_type']);
            $table->index(['receiver_id', 'receiver_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('messages');
    }
}
