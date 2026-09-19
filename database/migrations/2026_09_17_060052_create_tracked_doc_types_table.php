<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrackedDocTypesTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('tracked_doc_types')) {
            return;
        }

        Schema::create('tracked_doc_types', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nom')->unique();
            $table->date('date_limite')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('tracked_doc_types');
    }
}
