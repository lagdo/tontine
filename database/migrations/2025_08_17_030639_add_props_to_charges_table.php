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
        Schema::table('charges', function (Blueprint $table) {
            $table->string('name', 150)->nullable();
            $table->tinyInteger('type')->nullable();
            $table->tinyInteger('period')->nullable();
            $table->integer('amount')->nullable();
            $table->boolean('lendable')->nullable();
        });

        // Copy data from the current field to the old one
        $sql = <<<SQL
update charges c set
    name = (select name from charge_defs d where d.id = c.def_id),
    type = (select type from charge_defs d where d.id = c.def_id),
    period = (select period from charge_defs d where d.id = c.def_id),
    amount = (select amount from charge_defs d where d.id = c.def_id),
    lendable = (select lendable from charge_defs d where d.id = c.def_id)
SQL;
        DB::statement($sql);

        // Remove the nullable constraints
        Schema::table('charges', function (Blueprint $table) {
            $table->string('name', 150)->nullable(false)->change();
            $table->tinyInteger('type')->nullable(false)->change();
            $table->tinyInteger('period')->nullable(false)->change();
            $table->integer('amount')->nullable(false)->change();
            $table->boolean('lendable')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('charges', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('type');
            $table->dropColumn('period');
            $table->dropColumn('amount');
            $table->dropColumn('lendable');
        });
    }
};
