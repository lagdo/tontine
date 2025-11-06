<?php

namespace Ajax\App\Meeting\Session\Charge\Fixed;

trait ChargeTrait
{
    /**
     * @return string
     */
    protected function chargeBagId(): string
    {
        return 'charge.fixed.id';
    }

    /**
     * @return void
     */
    protected function showSettlementTotal(): void
    {
        $this->cl(SettlementTotal::class)->render();
        $this->cl(SettlementAll::class)->render();
    }
}
