<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->string('options', 1000);
        });

        Schema::create('country_currency', function (Blueprint $table) {
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
            $table->unique(['country_id', 'currency_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('country_currency');
        Schema::dropIfExists('currencies');
    }
}
