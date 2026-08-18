<?php

namespace Ajax\App\Meeting\Summary\Pool;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\CallableAction;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function trans;

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
    protected function getPool(): void
    {
        if($this->action()->func() === 'pool')
        {
            $this->bag('summary')->set('pool.id', $this->action()->args()[0]);
        }
        $session = $this->stash()->get('summary.session');
        $poolId = (int)$this->bag('summary')->get('pool.id');
        $pool = $this->poolService->getPool($session, $poolId);
        if(!$pool)
        {
            throw new MessageException(trans('tontine.session.errors.disabled'));
        }

        $this->stash()->set('summary.pool', $pool);
    }
}
