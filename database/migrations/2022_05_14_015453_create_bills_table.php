<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillsTable extends Migration
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
            $table->string('name', 100);
            $table->integer('amount');
            $table->date('issued_at')->nullable();
            $table->date('deadline')->nullable();
            $table->string('notes')->default('');
            $table->timestamps();
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id')->nullable();
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('round_id')->nullable();
            $table->foreign('round_id')->references('id')->on('rounds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bills');
    }
}
