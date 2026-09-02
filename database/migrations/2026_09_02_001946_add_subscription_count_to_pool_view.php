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
create or replace view v_pools as
    select p.id as pool_id, ss.day_date as start_date, se.day_date as end_date,
        (select count(vps.session_id) from v_pool_session vps where vps.pool_id=p.id)
            as sessions_count,
        (select count(*) from subscriptions su where su.pool_id=p.id)
            as subscriptions_count
        from pools p
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
create or replace view v_pools as
    select p.id as pool_id, ss.day_date as start_date, se.day_date as end_date,
        (select count(vps.session_id) from v_pool_session vps where vps.pool_id=p.id)
            as sessions_count
        from pools p
        inner join sessions ss on ss.id=p.start_sid
        inner join sessions se on se.id=p.end_sid
SQL;
        DB::statement($sql);
    }
};
