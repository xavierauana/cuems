<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSponsorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('sponsor_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('tel')->nullable();
            $table->text('address')->nullable();
            $table->unsignedInteger('delegate_id');
            $table->foreign('delegate_id')->references('id')->on('delegates')
                  ->onDelete('cascade');
            $table->unsignedInteger('sponsor_id');
            $table->foreign('sponsor_id')->references('id')->on('sponsors')
                  ->onDelete('cascade');

            $table->unique(['delegate_id', 'sponsor_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('sponsor_records');
    }
}
