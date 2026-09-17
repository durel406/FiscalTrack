<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSuiviFieldsToContribuablesTable extends Migration
{
    public function up()
    {
        Schema::table('contribuables', function (Blueprint $table) {
            $table->string('organisme')->nullable()->after('cat');          // DGI, CNPS, ou autre
            $table->string('lien_verification')->nullable()->after('organisme');
        });
    }
 
    public function down()
    {
        Schema::table('contribuables', function (Blueprint $table) {
            $table->dropColumn(['organisme', 'lien_verification']);
        });
    }
}
