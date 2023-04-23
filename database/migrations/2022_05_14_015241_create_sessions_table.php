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
        Schema::create('sessions', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('abbrev', 10)->nullable();
            $table->string('venue', 500)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->text('agenda')->nullable();
            $table->text('report')->nullable();
            $table->string('notes')->nullable();
            $table->datetime('start_at');
            $table->datetime('end_at');
            $table->unsignedBigInteger('round_id');
            $table->foreign('round_id')->references('id')->on('rounds');
            $table->unsignedBigInteger('host_id')->nullable();
            $table->foreign('host_id')->references('id')->on('members');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
};
