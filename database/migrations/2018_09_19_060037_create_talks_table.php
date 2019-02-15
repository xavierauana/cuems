<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTalksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('talks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->text('subtitle')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->unsignedInteger('session_id');
            $table->foreign('session_id')
                  ->references('id')
                  ->on('sessions')
                  ->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('speakers', function (Blueprint $table) {
            $table->unsignedInteger('talk_id');
            $table->foreign('talk_id')->references('id')->on('talks')
                  ->onDelete('cascade');

            $table->unsignedInteger('delegate_id');
            $table->foreign('delegate_id')->references('id')->on('delegates')
                  ->onDelete('cascade');

            $table->primary(['delegate_id', 'talk_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('speakers');
        Schema::dropIfExists('talks');
    }
}
