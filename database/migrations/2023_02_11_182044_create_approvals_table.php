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
        Schema::create('approvals', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->boolean('valid')->default(false);
            $table->boolean('eo_file')->default(false);
            $table->boolean('eo_exp')->default(false);
            $table->boolean('eo_limit')->default(false);
            $table->boolean('license_file')->default(false);
            $table->boolean('license')->default(false);
            $table->boolean('agreement')->default(false);
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
        Schema::dropIfExists('approvals');
    }
};
