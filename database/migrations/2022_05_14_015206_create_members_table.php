<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('email', 100)->default('');
            $table->string('phone', 20)->default('');
            $table->string('address', 500)->default('');
            $table->string('city', 100)->default('');
            $table->date('registered_at')->nullable();
            $table->date('birthday')->nullable();
            $table->boolean('active');
            $table->unsignedBigInteger('tontine_id');
            $table->foreign('tontine_id')->references('id')->on('tontines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
