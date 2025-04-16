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
        if(DB::connection()->getDriverName() === 'mysql')
        {
            // MySQL doesn't allow BLOB or TEXT columns to have default values.
            // We then need to set the default value after the column is created.
            Schema::table('pools', function (Blueprint $table) {
                $table->json('properties');
            });
            DB::table('pools')->update(['properties' => '{}']);
        }
        else
        {
            Schema::table('pools', function (Blueprint $table) {
                $table->json('properties')->default('{}');
            });
        }

        // Update the new field values with the seeder
        // Artisan::call('db:seed', ['--class' => 'PoolPropertiesSeeder']);

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
        // Do not rollback this migration if the database already contains data
        if(DB::table('tontines')->where('id', '>', 0)->exists())
        {
            throw new Exception('Rollback is not allowed on this migration.');
        }

        Schema::table('pools', function (Blueprint $table) {
            $table->dropColumn('properties');
        });
    }
};
