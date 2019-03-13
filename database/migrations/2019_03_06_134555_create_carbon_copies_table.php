<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarbonCopiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('carbon_copies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email');
            $table->enum('type', ['cc', 'bcc']);
            $table->unsignedInteger('notification_id');
            $table->foreign('notification_id')->references('id')
                  ->on('notifications')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('carbon_copies');
    }
}
