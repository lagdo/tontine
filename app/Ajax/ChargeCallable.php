<?php

namespace App\Ajax;

use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Charge as ChargeModel;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

/**
 * @before getCharge
 */
class ChargeCallable extends SessionCallable
{
    /**
     * @var ChargeModel|null
     */
    protected ?ChargeModel $charge;

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
         $chargeId = $this->target()->method() === 'home' ?
             $this->target()->args()[0] : $this->bag('meeting')->get('charge.id');
         $this->charge = $this->chargeService->getCharge($chargeId);
     }
  
    /**
     * @return void
     */
    protected function checkChargeEdit()
    {
        if(!$this->session || $this->session->closed)
        {
            throw new MessageException(trans('meeting.warnings.session.closed'), false);
        }
        if(!$this->charge || !$this->charge->active)
        {
            throw new MessageException(trans('meeting.warnings.charge.disabled'), false);
        }
    }
}
