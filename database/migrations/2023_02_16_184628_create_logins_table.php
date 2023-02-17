<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logins', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->string('AON Edge')->default('{"direct":false,"username":"","password":""}');
            $table->string('Beyond Flood')->default('{"direct":false,"username":"","password":""}');
            $table->string('Dual Flood')->default('{"direct":false,"username":"","password":""}');
            $table->string('Flow Flood')->default('{"direct":false,"username":"","password":""}');
            $table->string('Neptune')->default('{"direct":false,"username":"","password":""}');
            $table->string('Palomar')->default('{"direct":false,"username":"","password":""}');
            $table->string('Sterling')->default('{"direct":false,"username":"","password":""}');
            $table->string('Wright')->default('{"direct":false,"username":"","password":""}');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logins');
    }
};
