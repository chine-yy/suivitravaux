<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->boolean('est_envoye')->default(false)->after('statut');
            $table->timestamp('date_envoi')->nullable()->after('est_envoye');
        });
    }

    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropColumn(['est_envoye', 'date_envoi']);
        });
    }
};
