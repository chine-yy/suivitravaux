<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            if (!Schema::hasColumn('contrats', 'est_envoye_partenaire')) {
                $table->boolean('est_envoye_partenaire')->default(false)->after('created_by');
                $table->timestamp('date_envoi_partenaire')->nullable()->after('est_envoye_partenaire');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('contrats', 'est_envoye_partenaire')) {
                $columns[] = 'est_envoye_partenaire';
            }
            if (Schema::hasColumn('contrats', 'date_envoi_partenaire')) {
                $columns[] = 'date_envoi_partenaire';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
