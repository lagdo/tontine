<?php

namespace Ajax\App\Report\Round;

trait PoolTrait
{
    protected function getPools(): void
    {
        $round = $this->tenantService->round();
        $figures = $this->summaryService->getFigures($round); 
        $pools = $this->summaryService->getPoolsBalance($figures);
        $funds = $this->summaryService->getFunds($round); 
        $this->view()->share('figures', $figures);
        $this->view()->share('pools', $pools);
        $this->view()->share('funds', $funds);
    }
}
