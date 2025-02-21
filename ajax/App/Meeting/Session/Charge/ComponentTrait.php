<?php

namespace Ajax\App\Meeting\Session\Charge;

use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

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
     * @return void
     */

    protected function getCharge()
    {
        if($this->target()->method() === 'charge')
        {
            $this->bag('meeting')->set('charge.id', $this->target()->args()[0]);
        }
        $chargeId = $this->bag('meeting')->get('charge.id');
        $this->stash()->set('meeting.session.charge', $this->chargeService->getCharge($chargeId));
    }
  
    /**
     * @return void
     */
    protected function checkChargeEdit()
    {
        $session = $this->stash()->get('meeting.session');
        if(!$session || $session->closed)
        {
            throw new MessageException(trans('meeting.warnings.session.closed'), false);
        }
        $charge = $this->stash()->get('meeting.session.charge');
        if(!$charge || !$charge->active)
        {
            throw new MessageException(trans('meeting.warnings.charge.disabled'), false);
        }
    }
}
