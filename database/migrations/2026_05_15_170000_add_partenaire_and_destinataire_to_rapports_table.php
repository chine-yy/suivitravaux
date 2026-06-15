<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            if (Schema::hasColumn('rapports', 'client_id')) {
                $table->renameColumn('client_id', 'partenaire_id');
            } elseif (!Schema::hasColumn('rapports', 'partenaire_id')) {
                $table->unsignedBigInteger('partenaire_id')->nullable()->after('auteur_id');
                $table->foreign('partenaire_id')->references('id')->on('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('rapports', 'destinataire_type')) {
                $table->string('destinataire_type')->nullable()->after('avancement_constate');
            }
            if (!Schema::hasColumn('rapports', 'destinataire_id')) {
                $table->unsignedBigInteger('destinataire_id')->nullable()->after('destinataire_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rapports', function (Blueprint $table) {
            if (Schema::hasColumn('rapports', 'partenaire_id')) {
                $table->dropForeign(['partenaire_id']);
                $table->dropColumn(['partenaire_id', 'destinataire_type', 'destinataire_id']);
            } elseif (Schema::hasColumn('rapports', 'client_id')) {
                $table->dropForeign(['client_id']);
                $table->dropColumn(['client_id', 'destinataire_type', 'destinataire_id']);
            }
        });
    }
};
