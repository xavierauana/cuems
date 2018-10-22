<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('template');
            $table->string('name');
            $table->string('from_name')->nullable();
            $table->string('from_email')->nullable();
            $table->string('subject')->nullable();
            $table->boolean('include_ticket')->default(false);
            $table->unsignedInteger('event');
            $table->nullableMorphs('recipient');
            $table->unsignedInteger('role_id')->nullable();
            $table->dateTime('schedule')->nullable();
            $table->boolean('is_sent')->default(false);

            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')
                  ->on('notifications')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('notifications');
    }
}
