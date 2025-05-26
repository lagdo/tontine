<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Round;

class ChargeService
{
    /**
     * Get a single charge.
     *
     * @param Round $round
     * @param int $chargeId
     *
     * @return Charge|null
     */
    public function getCharge(Round $round, int $chargeId): ?Charge
    {
        return $round->charges()->find($chargeId);
    }
}
