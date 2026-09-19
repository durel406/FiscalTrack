<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeclarationStatutsTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('declaration_statuts')) {
            return;
        }

        Schema::create('declaration_statuts', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedBigInteger('contribuable_id');
            $table->unsignedBigInteger('tracked_doc_type_id');

            // 'non_declare' par défaut ; 'penalite' est calculé automatiquement
            // côté application quand la date limite est dépassée.
            $table->enum('statut', ['non_declare', 'declare', 'penalite'])->default('non_declare');

            $table->timestamps();

            $table->foreign('contribuable_id')
                  ->references('id')->on('contribuables')->onDelete('cascade');
            $table->foreign('tracked_doc_type_id')
                  ->references('id')->on('tracked_doc_types')->onDelete('cascade');

            // Un seul statut possible par couple contribuable / document à suivre
            $table->unique(['contribuable_id', 'tracked_doc_type_id'], 'decl_statut_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('declaration_statuts');
    }
}
