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
        Schema::create('onboarding_info', function (Blueprint $table) {
            $table->string('rocket_id')->primary();
            $table->string('agency_name')->nullable();
            $table->string('dba_name')->nullable();
            $table->string('agent_name');
            $table->string('phone')->nullable();
            $table->string('email')->unique();
            $table->string('address')->nullable();
            $table->string('address1')->nullable();
            $table->string('address2')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->string('additional_states')->nullable();
            $table->string('carriers')->nullable();
            $table->boolean('aon')->default(false);
            $table->boolean('beyond')->default(false);
            $table->boolean('dual')->default(false);
            $table->boolean('flow')->default(false);
            $table->boolean('neptune')->default(false);
            $table->boolean('palomar')->default(false);
            $table->boolean('sterling')->default(false);
            $table->boolean('wright')->default(false);
            $table->string('agency_license')->nullable();
            $table->date('agency_license_exp')->nullable();
            $table->string('agency_tax_id')->nullable();
            $table->string('agent_license')->nullable();
            $table->date('agent_license_eff')->nullable();
            $table->date('agent_license_exp')->nullable();
            $table->string('agent_license_file')->nullable();
            $table->string('agent_npn')->nullable();
            $table->string('eo_policy')->nullable();
            $table->string('eo_insurer')->nullable();
            $table->integer('eo_limit')->nullable();
            $table->date('eo_exp')->nullable();
            $table->string('eo')->nullable();
            $table->string('agency_license_file')->nullable();
            $table->string('agency_type')->nullable();
            $table->string('agency_logo')->nullable();
            $table->string('stage')->nullable();
            $table->boolean('completed')->default(false);
            $table->string('document_id')->nullable();
            $table->string('document_embed')->nullable();
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
        Schema::dropIfExists('onboarding_infos');
    }
};
