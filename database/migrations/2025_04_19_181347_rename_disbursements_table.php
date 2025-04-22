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
        $fields = 'id,amount,charge_lendable,comment,member_id,session_id,category_id,charge_id';
        DB::statement("INSERT INTO $target($fields) SELECT $fields FROM $source ORDER BY id ASC");
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outflows', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->boolean('charge_lendable')->default(true);
            $table->string('comment', 150);
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('charge_id')->nullable();
            $table->foreign('charge_id')->references('id')->on('charges');
        });

        $this->copyData('disbursements', 'outflows');
        DB::table('categories')->where('item_type', 'disbursement')->update([
            'item_type' => 'outflow',
        ]);

        Schema::dropIfExists('disbursements');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('disbursements', function (Blueprint $table) {
            $table->id();
            $table->integer('amount');
            $table->boolean('charge_lendable')->default(true);
            $table->string('comment', 150);
            $table->unsignedBigInteger('member_id')->nullable();
            $table->foreign('member_id')->references('id')->on('members');
            $table->unsignedBigInteger('session_id');
            $table->foreign('session_id')->references('id')->on('sessions');
            $table->unsignedBigInteger('category_id');
            $table->foreign('category_id')->references('id')->on('categories');
            $table->unsignedBigInteger('charge_id')->nullable();
            $table->foreign('charge_id')->references('id')->on('charges');
        });

        $this->copyData('outflows', 'disbursements');
        DB::table('categories')->where('item_type', 'outflow')->update([
            'item_type' => 'disbursement',
        ]);

        Schema::dropIfExists('outflows');
    }
};
