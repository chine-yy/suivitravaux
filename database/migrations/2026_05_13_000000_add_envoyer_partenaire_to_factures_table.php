<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            if (Schema::hasColumn('factures', 'est_envoye_client')) {
                $table->renameColumn('est_envoye_client', 'est_envoye_partenaire');
                $table->renameColumn('date_envoi_client', 'date_envoi_partenaire');
            } elseif (!Schema::hasColumn('factures', 'est_envoye_partenaire')) {
                $table->boolean('est_envoye_partenaire')->default(false)->after('created_by');
                $table->timestamp('date_envoi_partenaire')->nullable()->after('est_envoye_partenaire');
            }
        });
    }

    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('factures', 'est_envoye_partenaire')) {
                $columns[] = 'est_envoye_partenaire';
            }
            if (Schema::hasColumn('factures', 'date_envoi_partenaire')) {
                $columns[] = 'date_envoi_partenaire';
            }
            if (Schema::hasColumn('factures', 'est_envoye_client')) {
                $columns[] = 'est_envoye_client';
            }
            if (Schema::hasColumn('factures', 'date_envoi_client')) {
                $columns[] = 'date_envoi_client';
            }
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
