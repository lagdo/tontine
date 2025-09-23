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
drop view if exists v_bills
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_bills as
    select b.bill_id, 0 as bill_type, b.member_id,
        b.charge_id, b.session_id, s.round_id
        from libre_bills b
            inner join sessions s on b.session_id = s.id
    union select b.bill_id, 1 as bill_type, b.member_id,
        b.charge_id, b.session_id, s.round_id
        from session_bills b
            inner join sessions s on b.session_id = s.id
    union select b.bill_id, 2 as bill_type, b.member_id,
        b.charge_id, fs.id as session_id, b.round_id
        from round_bills b
            inner join v_round_first_sessions fs on b.round_id = fs.round_id
    union select b.bill_id, 3 as bill_type, b.member_id,
        b.charge_id, fs.id as session_id, b.round_id
        from onetime_bills b
            inner join v_round_first_sessions fs on b.round_id = fs.round_id;
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);
    }
};
