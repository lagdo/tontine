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
        Schema::create('pool_rounds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pool_id');
            $table->foreign('pool_id')->references('id')->on('pools');
            $table->unsignedBigInteger('start_session_id');
            $table->foreign('start_session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('end_session_id');
            $table->foreign('end_session_id')->references('id')->on('sessions');
            $table->unique('pool_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_rounds');
    }
};
