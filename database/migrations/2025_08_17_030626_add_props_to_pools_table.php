<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pools', function (Blueprint $table) {
            $table->string('title', 300)->nullable();
            $table->integer('amount')->nullable();
            $table->boolean('deposit_fixed')->nullable();
            $table->boolean('deposit_lendable')->nullable();
            $table->boolean('remit_planned')->nullable();
            $table->boolean('remit_auction')->nullable();
        });

        // Copy data from the current field to the old one
        foreach(DB::table('pools')->cursor() as $pool)
        {
            $def = DB::table('pool_defs')
                ->where('id', $pool->def_id)
                ->first();
            $properties = json_decode($def->properties, false);
            DB::table('pools')
                ->where('id', $pool->id)
                ->update([
                    'title' => $def->title,
                    'amount' => $def->amount,
                    'deposit_fixed' => $properties->deposit->fixed,
                    'deposit_lendable' => $properties->deposit->lendable,
                    'remit_planned' => $properties->remit->planned,
                    'remit_auction' => $properties->remit->auction,
                ]);
        }

        // Remove the nullable constraints
        Schema::table('pools', function (Blueprint $table) {
            $table->string('title', 300)->nullable(false)->change();
            $table->integer('amount')->nullable(false)->change();
            $table->boolean('deposit_fixed')->nullable(false)->change();
            $table->boolean('deposit_lendable')->nullable(false)->change();
            $table->boolean('remit_planned')->nullable(false)->change();
            $table->boolean('remit_auction')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pools', function (Blueprint $table) {
            $table->dropColumn('title');
            $table->dropColumn('amount');
            $table->dropColumn('deposit_fixed');
            $table->dropColumn('deposit_lendable');
            $table->dropColumn('remit_planned');
            $table->dropColumn('remit_auction');
        });
    }
};
