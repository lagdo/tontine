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
        Schema::table('onetime_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable();
        });

        $sql = <<<SQL
update onetime_bills b set round_id =
    (select m.round_id from members m where m.id = b.member_id);
SQL;
        DB::statement($sql);

        Schema::table('onetime_bills', function (Blueprint $table) {
            $table->unsignedBigInteger('round_id')->nullable(false)->change();
            $table->foreign('round_id')->references('id')->on('rounds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('onetime_bills', function (Blueprint $table) {
            $table->dropColumn('round_id');
        });
    }
};
