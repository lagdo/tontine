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
        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('notes')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('tontine_id');
            $table->foreign('tontine_id')->references('id')->on('tontines');
        });

        Schema::table('fundings', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('funds');
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('funds');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
            $table->dropColumn('fund_id');
        });

        Schema::table('fundings', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
            $table->dropColumn('fund_id');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('active');
        });

        Schema::dropIfExists('funds');
    }
};
