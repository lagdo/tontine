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
        // Create a view for pools with their start and end dates,
        // taken from the corresponding sessions.
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

        // Create a view for pools with their numbers of sessions and disabled sessions.
        $sql = <<<SQL
create view v_pool_counters as select pp.id, pp.title, pp.amount,
    case
        when exists(select 1 from pool_rounds pra where pra.pool_id = pp.id)
            then (select count(*) from sessions sa inner join rounds ra
                on (sa.round_id = ra.id and ra.tontine_id = rp.tontine_id)
                where sa.start_at between
                    (select sb.start_at from pool_rounds prb, sessions sb
                        where prb.pool_id = pp.id and prb.start_session_id = sb.id) and
                    (select sc.start_at from pool_rounds prc, sessions sc
                        where prc.pool_id = pp.id and prc.end_session_id  = sc.id))
            else (select count(*) from sessions sd where sd.round_id = pp.round_id)
    end as sessions,
    case
        when exists(select 1 from pool_rounds prd where prd.pool_id = pp.id)
            then (select count(*) from pool_session_disabled psa inner join sessions se
                on (psa.session_id = se.id and psa.pool_id = pp.id)
                where se.start_at between
                    (select sf.start_at from pool_rounds pre, sessions sf
                        where pre.pool_id = pp.id and pre.start_session_id = sf.id) and
                    (select sg.start_at from pool_rounds prf, sessions sg
                        where prf.pool_id = pp.id and prf.end_session_id  = sg.id))
            else (select count(*) from pool_session_disabled psb where psb.pool_id = pp.id)
    end as disabled_sessions
from pools pp inner join rounds rp on pp.round_id = rp.id;
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
drop view if exists v_pools
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pool_counters
SQL;
        DB::statement($sql);
    }
};
