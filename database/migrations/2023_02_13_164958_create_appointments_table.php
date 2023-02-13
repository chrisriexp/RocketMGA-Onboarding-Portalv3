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
        Schema::create('appointments', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->string('aon')->default('{"direct":false,"user":"","pass":""}');
            $table->string('beyond')->default('{"direct":false,"id":""}');
            $table->string('dual')->default('{"direct":false,"pl":"","plc":""}');
            $table->string('flow')->default('{"direct":false,"id":""}');;
            $table->string('neptune')->default('{"direct":false,"id":""}');;
            $table->string('palomar')->default('{"direct":false,"id":""}');;
            $table->string('sterling')->default('{"direct":false,"id":""}');;
            $table->string('wright')->default('{"direct":false,"id":""}');;
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
        Schema::dropIfExists('appointments');
    }
};
