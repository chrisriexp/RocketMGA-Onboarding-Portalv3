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
        Schema::table('onboarding_info', function (Blueprint $table) {
            $table->string('flow_package')->nullable();
            $table->string('beyond_package')->nullable();
            $table->string('sterling_package')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('onboarding_info', function (Blueprint $table) {
            //
        });
    }
};
