<?php

namespace Ajax\App\Meeting\Summary\Charge;

use Jaxon\App\DataBag\DataBagContext;
use Jaxon\App\Stash\Stash;
use Jaxon\Attributes\Attribute\Inject;
use Jaxon\Request\CallableAction;
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
        if($this->action()->func() === 'charge')
        {
            $this->bag('summary')->set($chargeBagId, $this->action()->args()[0]);
        }
        $chargeId = $this->bag('summary')->get($chargeBagId);
        $charge = $this->chargeService->getCharge($this->round(), $chargeId);
        $this->stash()->set('summary.session.charge', $charge);
    }
}
