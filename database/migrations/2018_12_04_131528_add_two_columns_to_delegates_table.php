<?php

use App\Enums\DelegateDuplicationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTwoColumnsToDelegatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table(
            'delegates', function (Blueprint $table) {
            $table->string('is_duplicated')
                  ->default(DelegateDuplicationStatus::UNKNOWN);
            $table->boolean('is_verified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('delegates', function (Blueprint $table) {
            //
        });
    }
}
