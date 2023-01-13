<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTontinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tontines', function (Blueprint $table) {
            $table->id();
            $table->char('type', 1);
            $table->string('name', 100);
            $table->string('shortname', 25);
            $table->text('biography')->nullable();
            $table->string('email', 100)->default('');
            $table->string('phone', 100)->default('');
            $table->string('address', 500)->default('');
            $table->string('city', 100)->default('');
            $table->string('website', 100)->default('');
            $table->string('numbers', 200)->default('{}');
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedInteger('country_id');
            $table->foreign('country_id')->references('id')->on('countries');
            $table->unsignedInteger('currency_id');
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_tontine');

        Schema::dropIfExists('tontines');
    }
}
