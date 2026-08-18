<?php

namespace Ajax\App\Planning\Pool;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\CallableAction;
use Siak\Tontine\Service\Planning\PoolService;

trait PoolTrait
{
    /**
     * @var PoolService
     */
    #[Inject]
    protected PoolService $poolService;

    /**
     * Get the Jaxon request target
     *
     * @return CallableAction|null
     */
    abstract protected function action(): ?CallableAction;

    /**
     * Get the temp cache
     *
     * @return Stash
     */
    abstract protected function stash(): Stash;

    /**
     * Get a data bag.
     *
     * @param string  $sBagName
     *
     * @return DataBagContext
     */
    abstract protected function bag(string $sBagName): DataBagContext;

    /**
     * @return void
     */
    protected function getPool()
    {
        if($this->action()->func() === 'pool')
        {
            $poolId = $this->action()->args()[0];
            $this->bag('planning.pool')->set('pool.id', $poolId);
        }

        $poolId = (int)$this->bag('planning.pool')->get('pool.id');
        $pool = $this->poolService->getPool($this->round(), $poolId);
        $this->stash()->set('planning.pool', $pool);
    }
}
