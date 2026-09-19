<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateObligationsTable extends Migration
{
    public function up()
    {
        if (! Schema::hasTable('obligations')) {
            Schema::create('obligations', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('contribuable_id');
                $table->unsignedInteger('tracked_doc_type_id');
                $table->unsignedSmallInteger('annee');
                $table->string('periode', 20); // T1..T4, M01..M12, ANNUEL, AUTRE
                $table->date('date_limite')->nullable();
                $table->date('date_depot')->nullable();
                // a_declarer | declare | justificatif_depose
                $table->string('statut', 40)->default('a_declarer');
                $table->string('organisme', 120)->nullable();
                $table->unsignedBigInteger('montant')->nullable();
                $table->string('resultat_controle', 40)->nullable(); // en_regle | non_conforme | null
                $table->text('commentaire_controle')->nullable();
                $table->date('date_controle')->nullable();
                $table->timestamps();

                $table->foreign('contribuable_id')
                      ->references('id')->on('contribuables')->onDelete('cascade');
                $table->foreign('tracked_doc_type_id')
                      ->references('id')->on('tracked_doc_types')->onDelete('cascade');

                $table->unique(
                    ['contribuable_id', 'tracked_doc_type_id', 'annee', 'periode'],
                    'obligations_unique_suivi'
                );
                $table->index(['date_limite', 'statut']);
            });
        }

        // Migration des anciennes lignes declaration_statuts → obligations (année courante / AUTRE)
        if (Schema::hasTable('declaration_statuts') && Schema::hasTable('obligations')) {
            $annee = (int) date('Y');
            $rows = DB::table('declaration_statuts')->get();
            foreach ($rows as $row) {
                $exists = DB::table('obligations')->where([
                    'contribuable_id'     => $row->contribuable_id,
                    'tracked_doc_type_id' => $row->tracked_doc_type_id,
                    'annee'               => $annee,
                    'periode'             => 'AUTRE',
                ])->exists();
                if ($exists) {
                    continue;
                }

                $statut = $row->statut === 'declare' ? 'declare' : 'a_declarer';
                $dateLimite = DB::table('tracked_doc_types')
                    ->where('id', $row->tracked_doc_type_id)
                    ->value('date_limite');

                DB::table('obligations')->insert([
                    'contribuable_id'     => $row->contribuable_id,
                    'tracked_doc_type_id' => $row->tracked_doc_type_id,
                    'annee'               => $annee,
                    'periode'             => 'AUTRE',
                    'date_limite'         => $dateLimite,
                    'statut'              => $statut,
                    'created_at'          => now(),
                    'updated_at'          => now(),
                ]);
            }
        }
    }

    public function down()
    {
        Schema::dropIfExists('obligations');
    }
}
