<?php

namespace Ajax\App\Meeting\Session\Charge\Libre;

trait ChargeTrait
{
    /**
     * @return string
     */
    protected function chargeBagId(): string
    {
        return 'charge.libre.id';
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
