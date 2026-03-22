<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remitments view with final amount value.
        $sql = <<<SQL
create view v_remitments as select remitments.id, p.session_id,
    remitments.payable_id, p.subscription_id, s.pool_id,
    case
        when pools.deposit_fixed
            then pools.amount * v_pools.sessions_count
            else (select sum(vd.amount) from v_deposits vd where vd.session_id = p.session_id)
    end as amount
from remitments
    inner join payables p on p.id = remitments.payable_id
    inner join subscriptions s on s.id = p.subscription_id
    inner join pools on pools.id = s.pool_id
    inner join v_pools on v_pools.pool_id = s.pool_id
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_remitments
SQL;
        DB::statement($sql);
    }
};
