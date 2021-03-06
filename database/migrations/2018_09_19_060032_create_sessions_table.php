<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sessions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->text('sponsor')->nullable();
            $table->unsignedInteger('moderation_type')->nullable();
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                  ->references('id')
                  ->on('events')
                  ->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('moderators', function (Blueprint $table) {
            $table->unsignedInteger('delegate_id');
            $table->foreign('delegate_id')->references('id')->on('delegates')
                  ->onDelete('cascade');

            $table->unsignedInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions')
                  ->onDelete('cascade');

            $table->primary(['delegate_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('moderators');
        Schema::dropIfExists('sessions');
    }
}
