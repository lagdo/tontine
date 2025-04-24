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
drop view if exists v_pools
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_pools as
    select p.id as pool_id, ss.day_date as start_date, se.day_date as end_date,
        (select count(vps.session_id) from v_pool_session vps where vps.pool_id=p.id)
            as sessions_count
        from pools p
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_pool_session_disabled as
    select psd.* from pool_session_disabled psd
        inner join v_pools vp on vp.pool_id=psd.pool_id
        inner join sessions s on s.id=psd.session_id
            and s.day_date between vp.start_date and vp.end_date
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_pool_session_disabled
SQL;
        DB::statement($sql);

        $sql = <<<SQL
drop view if exists v_pools
SQL;
        DB::statement($sql);

        $sql = <<<SQL
create view v_pools as
    select p.id as pool_id, ss.day_date as start_date, se.day_date as end_date,
        (select count(s.id) from sessions s
            where s.day_date between ss.day_date and se.day_date
            and s.round_id in (select id from rounds r where r.guild_id=pd.guild_id))
            as sessions_count,
        (select count(psd.session_id)
            from pool_session_disabled psd
            inner join sessions pss on pss.id=psd.session_id
            where psd.pool_id=p.id and pss.day_date between ss.day_date and se.day_date)
            as disabled_sessions_count
        from pools p
        inner join pool_defs pd on pd.id=p.def_id
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);
    }
};
