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
}
