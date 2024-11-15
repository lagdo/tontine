<?php

namespace Ajax\App\Meeting\Session\Charge;

use Ajax\App\Meeting\MeetingPageComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

use function trans;

/**
 * @before getCharge
 */
abstract class ChargePageComponent extends MeetingPageComponent
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
