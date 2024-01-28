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
        Schema::create('guest_invites', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('status')->default(0);
            $table->boolean('active')->default(true);
            $table->date('expires_at');
            $table->unsignedBigInteger('host_id');
            $table->unsignedBigInteger('guest_id');
            $table->foreign('host_id')->references('id')->on('users');
            $table->foreign('guest_id')->references('id')->on('users');
            $table->unique(['host_id', 'guest_id']);
            $table->timestamps();
        });

        Schema::create('guest_tontine', function (Blueprint $table) {
            $table->json('access');
            $table->unsignedBigInteger('invite_id');
            $table->unsignedBigInteger('tontine_id');
            $table->foreign('invite_id')->references('id')->on('guest_invites');
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
        Schema::dropIfExists('guest_tontine');

        Schema::dropIfExists('guest_invites');
    }
};
