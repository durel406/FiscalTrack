<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContribuablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::create('contribuables', function (Blueprint $table) {
        $table->id();
        $table->string('nom');
        $table->string('niu')->nullable();
        $table->string('regime')->nullable();       // Réel, Simplifié, Classe, IGS Classe, NON PROFESSIONNEL, ou saisie libre
        $table->string('cat')->nullable();           // Catégorie / classe
        $table->enum('statut', ['active', 'inactive'])->default('active');
        $table->text('pass')->nullable();            // voir remarque sécurité plus bas

        $table->unsignedBigInteger('montant')->default(0);
        $table->unsignedBigInteger('t1')->default(0);
        $table->unsignedBigInteger('t2')->default(0);
        $table->unsignedBigInteger('t3')->default(0);
        $table->unsignedBigInteger('t4')->default(0);
        $table->unsignedBigInteger('tdl')->default(0);
        $table->unsignedBigInteger('impots')->default(0);
        $table->unsignedBigInteger('loyer')->default(0);
        $table->unsignedBigInteger('bail')->default(0);
        $table->unsignedBigInteger('precompte')->default(0);
        $table->unsignedBigInteger('timbre')->default(0);
        $table->unsignedBigInteger('frais_paiement')->default(0);
        $table->unsignedBigInteger('fs_paye')->default(0);
        $table->unsignedBigInteger('fs_non_paye')->default(0);

        $table->date('ai_igs')->nullable();
        $table->date('ai_bail')->nullable();
        $table->date('ai_precompte')->nullable();
        $table->date('q_igs')->nullable();
        $table->date('q_bail')->nullable();
        $table->date('q_precompte')->nullable();
        $table->date('acf_igs')->nullable();
        $table->date('acf_bail')->nullable();
        $table->date('acf_precompte')->nullable();

        $table->string('lieu')->nullable();
        $table->string('tel')->nullable();
        $table->timestamps();
    });
}
}

// database/migrations/xxxx_create_contribuables_table.php
