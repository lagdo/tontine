<?php

namespace Ajax\App\Planning\Pool;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\TargetInterface;
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
     * @return TargetInterface|null
     */
    abstract protected function target(): ?TargetInterface;

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
        if($this->target()->method() === 'pool')
        {
            $poolId = $this->target()->args()[0];
            $this->bag('planning.pool')->set('pool.id', $poolId);
        }

        $round = $this->stash()->get('tenant.round');
        $poolId = (int)$this->bag('planning.pool')->get('pool.id');
        $pool = $this->poolService->getPool($round, $poolId);
        $this->stash()->set('planning.pool', $pool);
    }
}
