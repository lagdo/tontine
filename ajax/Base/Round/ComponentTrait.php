<?php

namespace Ajax\Base\Round;

use Siak\Tontine\Model\Round;

use function view;

trait ComponentTrait
{
    /**
     * @return void
     */
    protected function getCurrentRound(): void
    {
        $roundId = $this->bag('tenant')->get('round.id', 0);
        $round = $roundId <= 0 ? null : $this->tenantService->getRound($roundId);
        if($round === null)
        {
            // Go back to the Guild section.
        }

        $this->tenantService->setRound($round);
        view()->share('currentRound', $round);
        $this->stash()->set('tenant.round', $round);
    }

    /**
     * @return Round
     */
    protected function round(): Round
    {
        return $this->stash()->get('tenant.round');
    }
}
