<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\Cache;
use App\Ajax\Web\Meeting\MeetingPageComponent;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Service\Meeting\Charge\BillService;
use Siak\Tontine\Service\Meeting\Charge\SettlementService;
use Siak\Tontine\Service\Tontine\ChargeService;

use function trans;

/**
 * @databag meeting
 * @before getSession
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
        $chargeId = $this->target()->method() === 'charge' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('charge.id');
        Cache::set('meeting.session.charge', $this->chargeService->getCharge($chargeId));
    }
  
    /**
     * @return void
     */
    protected function checkChargeEdit()
    {
        $session = Cache::get('meeting.session');
        if(!$session || $session->closed)
        {
            throw new MessageException(trans('meeting.warnings.session.closed'), false);
        }
        $charge = Cache::get('meeting.session.charge');
        if(!$charge || !$charge->active)
        {
            throw new MessageException(trans('meeting.warnings.charge.disabled'), false);
        }
    }
}
