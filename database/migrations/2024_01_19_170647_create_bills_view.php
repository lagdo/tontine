<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.tontine_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.tontine_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.tontine_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.start_at = (select min(s2.start_at) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select tontine_bills.bill_id, 3 as bill_type, members.name as member,
        tontine_bills.charge_id, members.tontine_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.tontine_id = members.tontine_id
            and s3.start_at = (select min(s4.start_at) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.tontine_id = members.tontine_id)) as session_id
    from tontine_bills inner join members on tontine_bills.member_id = members.id
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);
    }
};
