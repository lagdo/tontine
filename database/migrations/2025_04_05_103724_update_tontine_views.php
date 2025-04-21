<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_pools as select pp.id, rp.guild_id,
    case
        when exists(select 1 from pool_rounds pra where pra.pool_id = pp.id)
            then (select sa.start_at from pool_rounds prb inner join sessions sa
                on (prb.start_session_id = sa.id and prb.pool_id = pp.id))
            else (select min(start_at) from sessions sb where sb.round_id = pp.round_id)
    end as start_at,
    case
        when exists(select 1 from pool_rounds prc where prc.pool_id = pp.id)
            then (select sc.start_at from pool_rounds prd inner join sessions sc
                on (prd.end_session_id = sc.id and prd.pool_id = pp.id))
            else (select max(start_at) from sessions sd where sd.round_id = pp.round_id)
    end as end_at
from pools pp inner join rounds rp on pp.round_id = rp.id;
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_bills as
    select libre_bills.bill_id, 0 as bill_type, members.name as member,
        libre_bills.charge_id, members.guild_id, sessions.round_id, libre_bills.session_id
    from libre_bills inner join sessions on libre_bills.session_id = sessions.id
        inner join members on libre_bills.member_id = members.id
    union
    select session_bills.bill_id, 1 as bill_type, members.name as member,
        session_bills.charge_id, members.guild_id, sessions.round_id, session_bills.session_id
    from session_bills inner join sessions on session_bills.session_id = sessions.id
        inner join members on session_bills.member_id = members.id
    union
    select round_bills.bill_id, 2 as bill_type, members.name as member,
        round_bills.charge_id, members.guild_id, round_bills.round_id,
        (select s1.id from sessions s1 where s1.round_id = round_bills.round_id
            and s1.start_at = (select min(s2.start_at) from sessions s2
            where s2.round_id = round_bills.round_id)) as session_id
    from round_bills inner join members on round_bills.member_id = members.id
    union
    select oneoff_bills.bill_id, 3 as bill_type, members.name as member,
        oneoff_bills.charge_id, members.guild_id, 0 as round_id,
        (select s3.id from sessions s3 inner join rounds r1 on s3.round_id = r1.id
            where r1.guild_id = members.guild_id
            and s3.start_at = (select min(s4.start_at) from sessions s4
            inner join rounds r2 on s4.round_id = r2.id
            where r2.guild_id = members.guild_id)) as session_id
    from oneoff_bills inner join members on oneoff_bills.member_id = members.id
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pool_session as
    select distinct p.id as pool_id, s.id as session_id
        from pools p, sessions s, rounds rp, rounds rs, guilds gp, guilds gs, pool_rounds pr
            where
                (p.round_id = rp.id and s.round_id = rp.id
                and not exists(select 1 from pool_rounds pra where pra.pool_id = p.id))
            or
                (p.round_id = rp.id and rp.guild_id = gp.id
                and s.round_id = rs.id and rs.guild_id = gs.id
                and gp.id = gs.id and pr.pool_id = p.id
                and (s.start_at between
                    (select sc.start_at from pool_rounds prc, sessions sc
                        where prc.pool_id = p.id and prc.start_session_id = sc.id) and
                    (select sd.start_at from pool_rounds prd, sessions sd
                        where prd.pool_id = p.id and prd.end_session_id  = sd.id)))
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_pools as select pp.id, rp.tontine_id,
    case
        when exists(select 1 from pool_rounds pra where pra.pool_id = pp.id)
            then (select sa.start_at from pool_rounds prb inner join sessions sa
                on (prb.start_session_id = sa.id and prb.pool_id = pp.id))
            else (select min(start_at) from sessions sb where sb.round_id = pp.round_id)
    end as start_at,
    case
        when exists(select 1 from pool_rounds prc where prc.pool_id = pp.id)
            then (select sc.start_at from pool_rounds prd inner join sessions sc
                on (prd.end_session_id = sc.id and prd.pool_id = pp.id))
            else (select max(start_at) from sessions sd where sd.round_id = pp.round_id)
    end as end_at
from pools pp inner join rounds rp on pp.round_id = rp.id;
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_bills
SQL;
        DB::statement($sql);

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

        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);

$sql = <<<SQL
create view v_pool_session as
    select distinct p.id as pool_id, s.id as session_id
        from pools p, sessions s, rounds rp, rounds rs, tontines tp, tontines ts, pool_rounds pr
            where
                (p.round_id = rp.id and s.round_id = rp.id
                and not exists(select 1 from pool_rounds pra where pra.pool_id = p.id))
            or
                (p.round_id = rp.id and rp.tontine_id = tp.id
                and s.round_id = rs.id and rs.tontine_id = ts.id
                and tp.id = ts.id and pr.pool_id = p.id
                and (s.start_at between
                    (select sc.start_at from pool_rounds prc, sessions sc
                        where prc.pool_id = p.id and prc.start_session_id = sc.id) and
                    (select sd.start_at from pool_rounds prd, sessions sd
                        where prd.pool_id = p.id and prd.end_session_id  = sd.id)))
SQL;
        DB::statement($sql);
    }
};
