<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Web\Meeting\MeetingComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

use function trans;

/**
 * @before getCharge
 */
abstract class ChargeComponent extends MeetingComponent
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
        $this->cache->set('meeting.session.charge', $this->chargeService->getCharge($chargeId));
    }
  
    /**
     * @return void
     */
    protected function checkChargeEdit()
    {
        $session = $this->cache->get('meeting.session');
        if(!$session || $session->closed)
        {
            throw new MessageException(trans('meeting.warnings.session.closed'), false);
        }
        $charge = $this->cache->get('meeting.session.charge');
        if(!$charge || !$charge->active)
        {
            throw new MessageException(trans('meeting.warnings.charge.disabled'), false);
        }
    }
}
