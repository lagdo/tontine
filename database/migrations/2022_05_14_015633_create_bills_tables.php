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
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('charge', 180);
            $table->integer('amount');
            $table->date('issued_at')->nullable();
            $table->date('deadline')->nullable();
            $table->string('notes')->nullable();
        });

        Schema::create('tontine_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id']);
        });

        Schema::create('round_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('round_id');
            $table->foreign('round_id')->references('id')->on('rounds');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id', 'round_id']);
        });

        Schema::create('session_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id', 'session_id']);
        });

        Schema::create('fine_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id', 'session_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fine_bills');
        Schema::dropIfExists('session_bills');
        Schema::dropIfExists('round_bills');
        Schema::dropIfExists('tontine_bills');
        Schema::dropIfExists('bills');
    }
};
