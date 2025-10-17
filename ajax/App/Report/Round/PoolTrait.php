<?php

namespace Ajax\App\Report\Round;

trait PoolTrait
{
    protected function getPools(): void
    {
        $round = $this->stash()->get('tenant.round');
        $session = $this->target()->method() !== 'select' ? null :
            $round->sessions()->opened()->find($this->target()->args()[0]);
        if(!$session)
        {
            $session = $round->sessions()->opened()
                ->orderBy('day_date', 'desc')
                ->first();
        }

        $figures = $this->summaryService->getFigures($round, $session); 
        $pools = $this->summaryService->getPoolsBalance($figures);
        $funds = $this->summaryService->getFunds($round); 

        $this->stash()->set('tenant.session', $session);

        $this->view()->share('figures', $figures);
        $this->view()->share('pools', $pools);
        $this->view()->share('funds', $funds);
    }
}
