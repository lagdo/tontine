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
        Schema::create('charges', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->tinyInteger('type');
            $table->tinyInteger('period');
            $table->integer('amount');
            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('tontine_id');
            $table->foreign('tontine_id')->references('id')->on('tontines');
            $table->unique(['name', 'tontine_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('charges');
    }
};
