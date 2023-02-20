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
            $table->string('AON Edge')->default('{"direct":false,"UserName":"","Password":""}');
            $table->string('Beyond Flood')->default('{"direct":false,"req_agency_id":"","platform_id":""}');
            $table->string('Dual Flood')->default('{"direct":false,"api-pl-key":"","api-plc-key":""}');
            $table->string('Flow Flood')->default('{"direct":false,"email":""}');;
            $table->string('Neptune')->default('{"direct":false,"agentNo":"","isRemote":true}');;
            $table->string('Palomar')->default('{"direct":false,"AgentId":""}');;
            $table->string('Sterling')->default('{"direct":false,"sterling_agent_id":""}');;
            $table->string('Wright')->default('{"direct":false,"agentNumber":""}');;
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
