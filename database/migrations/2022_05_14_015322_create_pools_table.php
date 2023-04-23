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
        Schema::create('pools', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->integer('amount');
            $table->string('notes')->nullable();
            $table->unsignedBigInteger('round_id');
            $table->foreign('round_id')->references('id')->on('rounds');
        });

        Schema::create('pool_session_disabled', function (Blueprint $table) {
            $table->unsignedBigInteger('pool_id');
            $table->foreign('pool_id')->references('id')->on('pools');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unique(['pool_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pool_session_disabled');
        Schema::dropIfExists('pools');
    }
};
