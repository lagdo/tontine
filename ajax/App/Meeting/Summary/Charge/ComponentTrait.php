<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\ChargeService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;

use function trans;

trait ComponentTrait
{
    /**
     * @di
     * @var ChargeService
     */
    protected ChargeService $chargeService;

    /**
     * @di
     * @var SettlementService
     */
    protected SettlementService $settlementService;

    /**
     * @di
     * @var BillService
     */
    protected BillService $billService;

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

    protected function getCharge(): void
    {
        if($this->target()->method() === 'charge')
        {
            $this->bag('summary')->set('charge.id', $this->target()->args()[0]);
        }
        $round = $this->stash()->get('tenant.round');
        $chargeId = $this->bag('summary')->get('charge.id');
        $charge = $this->chargeService->getCharge($round, $chargeId);
        $this->stash()->set('summary.session.charge', $charge);
    }
  
    /**
     * @return void
     */
    protected function checkChargeEdit(): void
    {
        $session = $this->stash()->get('summary.session');
        if(!$session || $session->closed)
        {
            throw new MessageException(trans('meeting.warnings.session.closed'), false);
        }
        $charge = $this->stash()->get('summary.session.charge');
        if(!$charge || !$charge->active)
        {
            throw new MessageException(trans('meeting.warnings.charge.disabled'), false);
        }
    }
}
