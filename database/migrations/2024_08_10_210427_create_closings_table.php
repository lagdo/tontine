<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
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
        Schema::create('closings', function (Blueprint $table) {
            $table->id();
            $table->json('options');
            $table->char('type', 1); // enum('type', ['r', 'i']);
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('fund_id');
            $table->foreign('fund_id')->references('id')->on('funds');
            $table->unique(['type', 'session_id', 'fund_id']);
        });

        Artisan::call('closing:copy-to-table');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Do not rollback this migration if the database already contains data
        if(DB::table('tontines')->where('id', '>', 0)->exists())
        {
            throw new Exception('Rollback is not allowed on this migration.');
        }

        Artisan::call('closing:copy-to-attrs');

        Schema::dropIfExists('closings');
    }
};
