<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUploadFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('upload_files', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('path');
            $table->string('disk');

            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')
                  ->onDelete('cascade');
            $table->timestamps();
        });
        Schema::create('notification_upload_file', function (Blueprint $table) {

            $table->unsignedInteger('notification_id');
            $table->foreign('notification_id')->references('id')
                  ->on('notifications')->onDelete('cascade');

            $table->unsignedInteger('upload_file_id');
            $table->foreign('upload_file_id')->references('id')
                  ->on('upload_files')->onDelete('cascade');

            $table->primary(['upload_file_id', 'notification_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('notification_upload_file');
        Schema::dropIfExists('upload_files');
    }
}
