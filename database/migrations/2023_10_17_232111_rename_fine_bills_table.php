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
        DB::statement("INSERT INTO $target(bill_id,charge_id,member_id,session_id) " .
            "SELECT bill_id,charge_id,member_id,session_id FROM $source ORDER BY id ASC");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // We create a new table in order to have the indexes renamed properly.
        Schema::create('libre_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id', 'session_id']);
        });

        $this->copyData('fine_bills', 'libre_bills');

        Schema::dropIfExists('fine_bills');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('fine_bills', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bill_id');
            $table->foreign('bill_id')->references('id')->on('bills');
            $table->unsignedBigInteger('charge_id');
            $table->foreign('charge_id')->references('id')->on('charges');
            $table->unsignedBigInteger('member_id');
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unique('bill_id');
            $table->unique(['charge_id', 'member_id', 'session_id']);
        });

        $this->copyData('libre_bills', 'fine_bills');

        Schema::dropIfExists('libre_bills');
    }
};
