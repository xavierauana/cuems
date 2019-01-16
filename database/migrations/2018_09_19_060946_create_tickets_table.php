<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('code');
            $table->unsignedInteger('price');
            $table->dateTime("start_at")->nullable();
            $table->dateTime("end_at")->nullable();
            $table->unsignedInteger("vacancy")->nullable();
            $table->boolean("is_public")->default(true);
            $table->string("template")->default('ticket');
            $table->text("note")->nullable();

            $table->unsignedInteger('event_id');
            $table->foreign('event_id')
                  ->references('id')
                  ->on('events')
                  ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['event_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('tickets');
    }
}
