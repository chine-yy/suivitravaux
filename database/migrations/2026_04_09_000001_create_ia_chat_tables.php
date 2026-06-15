<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateIaChatTables extends Migration
{
    public function up()
    {
        Schema::create('ia_chat_conversations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->string('user_type');
            $table->timestamps();
        });

        Schema::create('ia_chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('conversation_id');
            $table->string('role'); // user, assistant
            $table->text('content')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();

            $table->foreign('conversation_id')
                ->references('id')
                ->on('ia_chat_conversations')
                ->onDelete('cascade');
        });

        $hasIaChatBoxPermission = DB::table('permissions')->where('slug', 'activer-ia-chat-box')->exists();

        if (!$hasIaChatBoxPermission) {
            DB::table('permissions')->insert([
                [
                    'nom' => 'Activer IA Chat Box',
                    'slug' => 'activer-ia-chat-box',
                    'module' => 'ia-chat-box',
                    'action' => 'activer',
                    'group' => 'Communication',
                    'icon' => 'chat-square-quote',
                    'color' => 'primary',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // Nettoyage legacy: ne garder que IA Chat Box.
        DB::table('permissions')->where('slug', 'activer-ia-chat')->delete();
        DB::table('permissions')->where('module', 'ia-chat')->delete();
    }

    public function down()
    {
        Schema::dropIfExists('ia_chat_messages');
        Schema::dropIfExists('ia_chat_conversations');
        
        DB::table('permissions')->where('slug', 'activer-ia-chat')->delete();
        DB::table('permissions')->where('module', 'ia-chat')->delete();
    }
}
