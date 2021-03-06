<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBiddingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remittances', function (Blueprint $table) {
            $table->integer('amount_paid')->default(0);
        });

        Schema::create('biddings', function (Blueprint $table) {
            $table->id();
            $table->integer('amount_bid');
            $table->integer('amount_paid');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('biddings');

        Schema::table('remittances', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
}
