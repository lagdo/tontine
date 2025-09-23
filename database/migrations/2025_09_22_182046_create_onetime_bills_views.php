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
drop view if exists v_paid_onetime_bills
SQL;
        DB::statement($sql);

        // A view with te onetime bills already paid.
        $sql = <<<SQL
create view v_paid_onetime_bills as
    select distinct c.def_id as charge_def_id, m.def_id as member_def_id,
        s.round_id from settlements st
        inner join sessions s on st.session_id = s.id
        inner join onetime_bills as ob on st.bill_id = ob.bill_id
        inner join charges c on ob.charge_id = c.id
        inner join members m on ob.member_id = m.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_round_first_sessions
SQL;
        DB::statement($sql);

        // A view with the first session of each round.
        $sql = <<<SQL
create view v_round_first_sessions as
    select s.id, s.day_date, s.round_id from sessions s
        where s.day_date = (select min(sm.day_date)
            from sessions sm where s.round_id = sm.round_id);
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_round_first_sessions
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_paid_onetime_bills
SQL;
        DB::statement($sql);
    }
};
