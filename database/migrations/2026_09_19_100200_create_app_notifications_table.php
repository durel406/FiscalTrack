<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppNotificationsTable extends Migration
{
    public function up()
    {
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('obligation_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type', 20); // proche | aujourdhui | retard
            $table->string('title');
            $table->text('message');
            $table->string('dedupe_key')->unique(); // obligation|type|date_limite
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->foreign('obligation_id')
                  ->references('id')->on('obligations')
                  ->onDelete('cascade');
            $table->index(['read_at', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('app_notifications');
    }
}
