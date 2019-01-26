<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRecordConversionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('payment_record_conversions',
            function (Blueprint $table) {
                $table->unsignedInteger('payment_record_id');
                $table->foreign('payment_record_id')->references('id')
                      ->on('payment_records');
                $table->string('status');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('payment_record_conversions');
    }
}
