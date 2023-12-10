<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        $fields = 'amount,member_id,session_id,fund_id,created_at,updated_at';
        DB::statement("INSERT INTO $target($fields) SELECT $fields FROM $source ORDER BY id ASC");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We create a new table in order to have the indexes renamed properly.
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->timestamps();
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('categories');
        });

        $this->copyData('fundings', 'savings');

        Schema::dropIfExists('fundings');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('fundings', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->timestamps();
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('fund_id')->nullable();
            $table->foreign('fund_id')->references('id')->on('categories');
        });

        $this->copyData('savings', 'fundings');

        Schema::dropIfExists('savings');
    }
};
