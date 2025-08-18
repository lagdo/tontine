<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Deposits view with final amount value.
        $sql = <<<SQL
create view v_deposits as select deposits.id, deposits.session_id,
    deposits.receivable_id, r.subscription_id, s.pool_id,
    case
        when pools.deposit_fixed
            then pools.amount
            else deposits.amount
    end as amount
from deposits
    inner join receivables r on r.id = deposits.receivable_id
    inner join subscriptions s on s.id = r.subscription_id
    inner join pools on pools.id = s.pool_id
SQL;
        DB::statement($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = <<<SQL
drop view if exists v_deposits
SQL;
        DB::statement($sql);
    }
};
