<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
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
        $sql = <<<SQL
drop view if exists v_settlements
SQL;
        DB::statement($sql);
    }
};
