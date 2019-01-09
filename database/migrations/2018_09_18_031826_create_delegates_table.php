<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('delegates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('prefix');
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_male');
            $table->string('email');
            $table->string('mobile');
            $table->string('fax')->nullable();
            $table->string('position');
            $table->string('department');
            $table->string('institution');
            $table->text('address_1');
            $table->text('address_2')->nullable();
            $table->text('address_3')->nullable();
            $table->text('country');
            $table->unsignedInteger('registration_id');
            $table->string('training_organisation')->nullable();
            $table->string('training_organisation_address')->nullable();
            $table->string('supervisor')->nullable();
            $table->string('training_position')->nullable();
            $table->string('duplicated_with')->nullable();
            $table->unsignedInteger('event_id');
            $table->foreign('event_id')->references('id')->on('events')
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
        Schema::dropIfExists('delegates');
    }
}
