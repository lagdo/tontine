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
        Schema::table('charges', function (Blueprint $table) {
            $table->boolean('lendable')->default(false);
        });
        Schema::table('bills', function (Blueprint $table) {
            $table->boolean('lendable')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->dropColumn('lendable');
        });
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn('lendable');
        });
    }
};
