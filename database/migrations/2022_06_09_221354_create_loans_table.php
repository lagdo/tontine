<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remittances', function (Blueprint $table) {
            $table->integer('interest')->default(0);
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->integer('interest');
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
        Schema::dropIfExists('loans');

        Schema::table('remittances', function (Blueprint $table) {
            $table->dropColumn('interest');
        });
    }
}
