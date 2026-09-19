<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddObligationIdToDocumentsAndPeriodicite extends Migration
{
    public function up()
    {
        if (Schema::hasTable('documents') && ! Schema::hasColumn('documents', 'obligation_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->unsignedInteger('obligation_id')->nullable()->after('contribuable_id');
                $table->foreign('obligation_id')
                      ->references('id')->on('obligations')
                      ->onDelete('set null');
            });
        }

        if (Schema::hasTable('tracked_doc_types') && ! Schema::hasColumn('tracked_doc_types', 'periodicite')) {
            Schema::table('tracked_doc_types', function (Blueprint $table) {
                $table->string('periodicite', 20)->default('libre')->after('nom');
            });
        }

        if (Schema::hasTable('tracked_doc_types') && ! Schema::hasColumn('tracked_doc_types', 'organisme_defaut')) {
            Schema::table('tracked_doc_types', function (Blueprint $table) {
                $table->string('organisme_defaut', 40)->nullable()->after('periodicite');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('documents') && Schema::hasColumn('documents', 'obligation_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropForeign(['obligation_id']);
                $table->dropColumn('obligation_id');
            });
        }

        if (Schema::hasTable('tracked_doc_types')) {
            Schema::table('tracked_doc_types', function (Blueprint $table) {
                $cols = [];
                if (Schema::hasColumn('tracked_doc_types', 'organisme_defaut')) {
                    $cols[] = 'organisme_defaut';
                }
                if (Schema::hasColumn('tracked_doc_types', 'periodicite')) {
                    $cols[] = 'periodicite';
                }
                if ($cols) {
                    $table->dropColumn($cols);
                }
            });
        }
    }
}
