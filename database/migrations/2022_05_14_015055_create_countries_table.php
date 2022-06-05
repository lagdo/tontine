<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCountriesTable extends Migration
{
    /**
     * Make changes to the database.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('countries', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->string('code', 2);
            $table->string('phone', 3);
            $table->string('languages', 300);
            $table->string('operators', 300);
            $table->unique('code');
            $table->unique('phone');
        });
    }

    /**
     * Revert the changes to the database.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::drop('countries');
    }
}
