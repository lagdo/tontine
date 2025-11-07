<?php

namespace Ajax\App\Meeting\Summary\Charge\Fixed;

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
