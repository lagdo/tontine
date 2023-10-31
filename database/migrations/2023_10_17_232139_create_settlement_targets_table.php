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
        Schema::create('settlement_targets', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->boolean('global')->default(false);
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('deadline_id');
            $table->foreign('deadline_id')->references('id')->on('sessions');
            $table->unique(['charge_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settlement_targets');
    }
};
