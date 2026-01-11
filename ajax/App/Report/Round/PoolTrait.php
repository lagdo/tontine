<?php

namespace Ajax\App\Report\Round;

trait PoolTrait
{
    protected function getPools(): void
    {
        $session = $this->target()->method() !== 'select' ? null :
            $this->round()->sessions()->active()->find($this->target()->args()[0]);
        if(!$session)
        {
            $session = $this->round()->sessions()->active()
                ->orderBy('day_date', 'desc')
                ->first();
        }

        $figures = $this->summaryService->getFigures($this->round(), $session); 
        $pools = $this->summaryService->getPoolsBalance($figures);
        $funds = $this->summaryService->getFunds($this->round()); 

        $this->stash()->set('tenant.session', $session);

        $this->view()->share('figures', $figures);
        $this->view()->share('pools', $pools);
        $this->view()->share('funds', $funds);
    }
}
