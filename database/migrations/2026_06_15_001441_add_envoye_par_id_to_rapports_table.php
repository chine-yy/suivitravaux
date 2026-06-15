<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->foreignId('envoye_par_id')->nullable()->after('auteur_id')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            $table->dropForeign(['envoye_par_id']);
            $table->dropColumn('envoye_par_id');
        });
    }
};
