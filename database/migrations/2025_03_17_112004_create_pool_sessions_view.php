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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_pool_session
SQL;
        DB::statement($sql);
    }
};
