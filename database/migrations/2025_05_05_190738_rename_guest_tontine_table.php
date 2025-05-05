<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * @param string $source
     * @param string $target
     *
     * @return void
     */
    private function copyData(string $source, string $target)
    {
        $fields = 'access,invite_id,guild_id';
        DB::statement("INSERT INTO $target($fields) SELECT $fields FROM $source");
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guest_options', function (Blueprint $table) {
            $table->json('access');
            $table->unsignedBigInteger('invite_id');
            $table->unsignedBigInteger('guild_id');
            $table->foreign('invite_id')->references('id')->on('guest_invites');
            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->unique(['invite_id', 'guild_id']);
        });

        $this->copyData('guest_tontine', 'guest_options');

        Schema::dropIfExists('guest_tontine');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('guest_tontine', function (Blueprint $table) {
            $table->json('access');
            $table->unsignedBigInteger('invite_id');
            $table->unsignedBigInteger('guild_id');
            $table->foreign('invite_id')->references('id')->on('guest_invites');
            $table->foreign('guild_id')->references('id')->on('guilds');
            $table->unique(['invite_id', 'guild_id']);
        });

        $this->copyData('guest_options', 'guest_tontine');

        Schema::dropIfExists('guest_options');
    }
};
