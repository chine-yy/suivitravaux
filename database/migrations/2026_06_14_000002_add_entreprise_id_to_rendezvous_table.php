<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rendezvous', function (Blueprint $table) {
            if (!Schema::hasColumn('rendezvous', 'entreprise_id')) {
                $table->unsignedBigInteger('entreprise_id')->nullable()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rendezvous', function (Blueprint $table) {
            if (Schema::hasColumn('rendezvous', 'entreprise_id')) {
                $table->dropColumn('entreprise_id');
            }
        });
    }
};
