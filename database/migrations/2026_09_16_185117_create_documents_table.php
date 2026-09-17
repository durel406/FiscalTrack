<?php
/*
|--------------------------------------------------------------------------
| database/migrations/2026_09_16_000000_create_documents_table.php
|--------------------------------------------------------------------------
| Créez le fichier avec :  php artisan make:migration create_documents_table
| puis collez le contenu des méthodes up() / down() ci-dessous.
*/

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDocumentsTable extends Migration
{
    public function up()
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->increments('id');

            $table->string('nom');
            $table->string('type')->nullable();          // Avis d'imposition, Quittance, ATMP, ACS...
            $table->string('fournisseur')->nullable();
            $table->unsignedBigInteger('montant')->default(0);

            // Contribuable associé (clé étrangère vers la table contribuables)
            $table->unsignedInteger('contribuable_id')->nullable();
            $table->foreign('contribuable_id')
                  ->references('id')->on('contribuables')
                  ->onDelete('set null');

            // Fichier numérique : on stocke le CHEMIN, jamais le contenu du fichier
            $table->string('fichier_path')->nullable();   // ex: documents/aBc123.pdf
            $table->string('fichier_nom')->nullable();    // nom d'origine, ex: quittance_igs.pdf
            $table->string('fichier_mime')->nullable();   // ex: application/pdf

            // Archivage : NULL = document actif, une date = document archivé
            $table->timestamp('archived_at')->nullable();

            $table->timestamps(); // created_at = date de création, updated_at = dernière modification
        });
    }

    public function down()
    {
        Schema::dropIfExists('documents');
    }
}