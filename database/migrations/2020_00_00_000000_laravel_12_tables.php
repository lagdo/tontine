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
        // 2019_08_19_000000_create_failed_jobs_table
        Schema::dropIfExists('failed_jobs');

        // 2014_10_13_000000_create_sessions_table
        Schema::dropIfExists('auth_sessions');

        // 2014_10_13_000000_create_cache_table
        Schema::dropIfExists('cache');
        Schema::dropIfExists('cache_locks');

        // 2014_10_12_100000_create_password_resets_table
        Schema::dropIfExists('password_resets');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 2014_10_12_100000_create_password_resets_table
        Schema::create('password_resets', function (Blueprint $table) {
            $table->string('email', 100)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 2014_10_13_000000_create_cache_table
        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // 2014_10_13_000000_create_sessions_table
        Schema::create('auth_sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 2019_08_19_000000_create_failed_jobs_table
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }
};
