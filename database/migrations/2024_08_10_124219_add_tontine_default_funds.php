<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
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
        // The "2023_12_08_134558_rename_fundings_table" has introduced a bug in
        // the fund_id field definition in the savings table. Let fix it first.
        Schema::table('savings', function (Blueprint $table) {
            $table->dropForeign(['fund_id']);
        });
        Schema::table('savings', function (Blueprint $table) {
            $table->foreign('fund_id')->references('id')->on('funds');
        });

        Artisan::call('fund:create-defaults');

        Schema::table('savings', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable(false)->change();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('savings', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable()->change();
        });

        Schema::table('loans', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable()->change();
        });

        Artisan::call('fund:delete-defaults');
    }
};
