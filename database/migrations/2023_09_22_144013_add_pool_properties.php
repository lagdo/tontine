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
        Schema::table('pools', function (Blueprint $table) {
            $table->json('properties')->default('{}');
        });

        // Update the new field values with the seeder
        Artisan::call('db:seed', ['--class' => 'PoolPropertiesSeeder']);

        // Todo: uncomment in a future release
        // Schema::table('tontines', function (Blueprint $table) {
        //     $table->dropColumn('type');
        // });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Todo: uncomment in a future release
        // Schema::table('tontines', function (Blueprint $table) {
        //     $table->char('type', 1); // enum('type', ['m', 'f', 'l']);
        // });

        Schema::table('pools', function (Blueprint $table) {
            $table->dropColumn('properties');
        });
    }
};
