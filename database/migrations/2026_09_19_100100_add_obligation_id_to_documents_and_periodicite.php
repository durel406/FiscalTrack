<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObligationIdToDocumentsAndPeriodicite extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->unsignedInteger('obligation_id')->nullable()->after('contribuable_id');
            $table->foreign('obligation_id')
                  ->references('id')->on('obligations')
                  ->onDelete('set null');
        });

        Schema::table('tracked_doc_types', function (Blueprint $table) {
            // mensuelle | trimestrielle | annuelle | libre
            $table->string('periodicite', 20)->default('libre')->after('nom');
            $table->string('organisme_defaut', 40)->nullable()->after('periodicite');
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['obligation_id']);
            $table->dropColumn('obligation_id');
        });

        Schema::table('tracked_doc_types', function (Blueprint $table) {
            $table->dropColumn(['periodicite', 'organisme_defaut']);
        });
    }
}
