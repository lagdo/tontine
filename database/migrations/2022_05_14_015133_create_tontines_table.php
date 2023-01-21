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
            $table->char('type', 1); // enum('type', ['m', 'f']);
            $table->string('name', 100);
            $table->string('shortname', 25);
            $table->text('biography')->nullable();
            $table->string('email', 100)->default('');
            $table->string('phone', 100)->default('');
            $table->string('address', 500)->default('');
            $table->string('city', 100)->default('');
            $table->string('website', 100)->default('');
            $table->string('country_code', 2);
            $table->string('currency_code', 3);
            $table->timestamps();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tontines');
    }
}
