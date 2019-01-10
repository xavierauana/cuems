<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedDecimal('amount');
            $table->text('note')->nullable();
            $table->date('date')->nullable();

            $table->unsignedInteger('category_id');
            $table->foreign('category_id')->references('id')
                  ->on('expense_categories')
                  ->onDelete('cascade');

            $table->unsignedInteger('vendor_id');
            $table->foreign('vendor_id')->references('id')->on('vendors')
                  ->onDelete('cascade');
            $table->string('vendor_contact_person')->nullable();
            $table->string('vendor_contact_number')->nullable();
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
        Schema::dropIfExists('expenses');
    }
}
