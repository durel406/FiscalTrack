<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropDateLimiteFromTrackedDocTypes extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('tracked_doc_types') || ! Schema::hasColumn('tracked_doc_types', 'date_limite')) {
            return;
        }

        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // Nettoyage d'une tentative précédente
            if (Schema::hasTable('tracked_doc_types_old_dl')) {
                Schema::drop('tracked_doc_types_old_dl');
            }

            // L'index unique SQLite survit au rename — le supprimer d'abord
            try {
                DB::statement('DROP INDEX IF EXISTS tracked_doc_types_nom_unique');
            } catch (\Exception $e) {
                // ignore
            }

            Schema::rename('tracked_doc_types', 'tracked_doc_types_old_dl');

            Schema::create('tracked_doc_types', function (Blueprint $table) {
                $table->increments('id');
                $table->string('nom');
                $table->string('periodicite', 40)->nullable()->default('libre');
                $table->string('organisme_defaut', 40)->nullable();
                $table->timestamps();
            });

            $rows = DB::table('tracked_doc_types_old_dl')->get();
            foreach ($rows as $row) {
                DB::table('tracked_doc_types')->insert([
                    'id'               => $row->id,
                    'nom'              => $row->nom,
                    'periodicite'      => isset($row->periodicite) ? $row->periodicite : 'libre',
                    'organisme_defaut' => isset($row->organisme_defaut) ? $row->organisme_defaut : null,
                    'created_at'       => $row->created_at,
                    'updated_at'       => $row->updated_at,
                ]);
            }

            Schema::drop('tracked_doc_types_old_dl');

            try {
                DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS tracked_doc_types_nom_unique ON tracked_doc_types (nom)');
            } catch (\Exception $e) {
                // ignore
            }

            return;
        }

        Schema::table('tracked_doc_types', function (Blueprint $table) {
            $table->dropColumn('date_limite');
        });
    }

    public function down()
    {
        if (Schema::hasTable('tracked_doc_types') && ! Schema::hasColumn('tracked_doc_types', 'date_limite')) {
            Schema::table('tracked_doc_types', function (Blueprint $table) {
                $table->date('date_limite')->nullable()->after('nom');
            });
        }
    }
}
