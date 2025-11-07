<?php

namespace Ajax\App\Meeting\Summary\Charge\Libre;

trait ChargeTrait
{
    /**
     * @return string
     */
    protected function chargeBagId(): string
    {
        return 'charge.fixed.id';
    }
}
