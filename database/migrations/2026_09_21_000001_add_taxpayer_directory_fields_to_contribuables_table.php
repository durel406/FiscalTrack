<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTaxpayerDirectoryFieldsToContribuablesTable extends Migration
{
    public function up()
    {
        Schema::table('contribuables', function (Blueprint $table) {
            $table->string('nom_raison_sociale')->nullable()->after('nom');
            $table->string('prenom_sigle')->nullable()->after('nom_raison_sociale');
            $table->string('activite_principale')->nullable()->after('niu');
            $table->string('centre_rattachement')->nullable()->after('cat');
            $table->string('ville')->nullable()->after('centre_rattachement');
            $table->string('quartier')->nullable()->after('ville');
            $table->string('lieux_dit')->nullable()->after('quartier');
        });

        DB::table('contribuables')->whereNull('nom_raison_sociale')->update([
            'nom_raison_sociale' => DB::raw('nom'),
        ]);
    }

    public function down()
    {
        Schema::table('contribuables', function (Blueprint $table) {
            $table->dropColumn([
                'nom_raison_sociale', 'prenom_sigle', 'activite_principale',
                'centre_rattachement', 'ville', 'quartier', 'lieux_dit',
            ]);
        });
    }
}