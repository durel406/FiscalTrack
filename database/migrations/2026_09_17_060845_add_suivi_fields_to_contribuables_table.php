<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuiviFieldsToContribuablesTable extends Migration
{
    public function up()
    {
        if (! Schema::hasColumn('contribuables', 'organisme')) {
            Schema::table('contribuables', function (Blueprint $table) {
                $table->string('organisme')->nullable()->after('cat');
            });
        }

        if (! Schema::hasColumn('contribuables', 'lien_verification')) {
            Schema::table('contribuables', function (Blueprint $table) {
                $table->string('lien_verification')->nullable()->after('organisme');
            });
        }
    }

    public function down()
    {
        Schema::table('contribuables', function (Blueprint $table) {
            $cols = [];
            if (Schema::hasColumn('contribuables', 'organisme')) {
                $cols[] = 'organisme';
            }
            if (Schema::hasColumn('contribuables', 'lien_verification')) {
                $cols[] = 'lien_verification';
            }
            if ($cols) {
                $table->dropColumn($cols);
            }
        });
    }
}
