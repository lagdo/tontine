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
        Schema::create('rounds', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->tinyInteger('status')->default(0);
            $table->string('notes')->nullable();
            $table->date('start_at');
            $table->date('end_at');
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
        Schema::dropIfExists('rounds');
    }
};
