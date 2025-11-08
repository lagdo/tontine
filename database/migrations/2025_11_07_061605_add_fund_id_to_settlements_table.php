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
        Schema::table('settlements', function (Blueprint $table) {
            $table->unsignedBigInteger('fund_id')->nullable()->default(null);
            $table->foreign('fund_id')->references('id')->on('funds');
        });

        // Drop the v_profit_transfers view.
        $sql = <<<SQL
drop view if exists v_profit_transfers
SQL;
        DB::statement($sql);

        // Create the v_profit_transfers view.
        $sql = <<<SQL
create view v_profit_transfers as
    select sv.fund_id, sv.session_id, sv.member_id, sv.amount, 1 as coef
        from savings sv
    union select st.fund_id, st.session_id, lb.member_id, b.amount, -1 as coef
        from settlements st
            inner join bills b on st.bill_id = b.id
            inner join libre_bills lb on lb.bill_id = b.id
            where st.fund_id is not null
    union select st.fund_id, st.session_id, sb.member_id, b.amount, -1 as coef
        from settlements st
            inner join bills b on st.bill_id = b.id
            inner join session_bills sb on sb.bill_id = b.id
            where st.fund_id is not null
    union select st.fund_id, st.session_id, rb.member_id, b.amount, -1 as coef
        from settlements st
            inner join bills b on st.bill_id = b.id
            inner join round_bills rb on rb.bill_id = b.id
            where st.fund_id is not null
    union select st.fund_id, st.session_id, ob.member_id, b.amount, -1 as coef
        from settlements st
            inner join bills b on st.bill_id = b.id
            inner join onetime_bills ob on ob.bill_id = b.id
            where st.fund_id is not null;
SQL;
        DB::statement($sql);

        // Re-create the v_settlements view.
        $sql = <<<SQL
drop view if exists v_settlements
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_settlements as
    select s.*, 0 as bill_type, lb.member_id, lb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join libre_bills lb on lb.bill_id = b.id
    union select s.*, 1 as bill_type, sb.member_id, sb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join session_bills sb on sb.bill_id = b.id
    union select s.*, 2 as bill_type, rb.member_id, rb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join round_bills rb on rb.bill_id = b.id
    union select s.*, 3 as bill_type, ob.member_id, ob.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join onetime_bills ob on ob.bill_id = b.id;
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the v_profit_transfers view.
        $sql = <<<SQL
drop view if exists v_profit_transfers
SQL;
        DB::statement($sql);

        // First drop the v_settlements view.
        $sql = <<<SQL
drop view if exists v_settlements
SQL;
        DB::statement($sql);

        Schema::table('settlements', function (Blueprint $table) {
            $table->dropColumn('fund_id');
        });

        // Then re-create the v_settlements view.
        $sql = <<<SQL
create view v_settlements as
    select s.*, 0 as bill_type, lb.member_id, lb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join libre_bills lb on lb.bill_id = b.id
    union select s.*, 1 as bill_type, sb.member_id, sb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join session_bills sb on sb.bill_id = b.id
    union select s.*, 2 as bill_type, rb.member_id, rb.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join round_bills rb on rb.bill_id = b.id
    union select s.*, 3 as bill_type, ob.member_id, ob.charge_id, b.amount
        from settlements s
            inner join bills b on s.bill_id = b.id
            inner join onetime_bills ob on ob.bill_id = b.id;
SQL;
        DB::statement($sql);
    }
};
