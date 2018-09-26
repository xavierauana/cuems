<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDelegateRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('delegate_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label');
            $table->string('code')->unique();
            $table->timestamps();
        });
        Schema::create('delegate_delegate_role', function (Blueprint $table) {
            $table->unsignedInteger('delegate_id');
            $table->unsignedInteger('delegate_role_id');
            $table->primary(['delegate_id', 'delegate_role_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('delegate_delegate_role');
        Schema::dropIfExists('delegate_roles');
    }
}
