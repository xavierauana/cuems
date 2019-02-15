<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportDelegatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('import_delegates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('batch_id');
            $table->unsignedInteger('delegate_id')->nullable();
            $table->longText('note')->nullable();
            $table->boolean('is_success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('import_delegates');
    }
}
