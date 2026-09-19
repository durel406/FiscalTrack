<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMontantToObligationsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('obligations') && ! Schema::hasColumn('obligations', 'montant')) {
            Schema::table('obligations', function (Blueprint $table) {
                $table->unsignedBigInteger('montant')->nullable()->after('organisme');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('obligations') && Schema::hasColumn('obligations', 'montant')) {
            Schema::table('obligations', function (Blueprint $table) {
                $table->dropColumn('montant');
            });
        }
    }
}
