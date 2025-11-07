<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\TargetInterface;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\ChargeService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;

trait ComponentTrait
{
    /**
     * @var ChargeService
     */
    #[Inject]
    protected ChargeService $chargeService;

    /**
     * @var SettlementService
     */
    #[Inject]
    protected SettlementService $settlementService;

    /**
     * @var BillService
     */
    #[Inject]
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

    /**
     * @return string
     */
    abstract protected function chargeBagId(): string;

    /**
     * @return void
     */

    protected function getCharge(): void
    {
        $chargeBagId = $this->chargeBagId();
        if($this->target()->method() === 'charge')
        {
            $this->bag('summary')->set($chargeBagId, $this->target()->args()[0]);
        }
        $round = $this->stash()->get('tenant.round');
        $chargeId = $this->bag('summary')->get($chargeBagId);
        $charge = $this->chargeService->getCharge($round, $chargeId);
        $this->stash()->set('summary.session.charge', $charge);
    }
}
