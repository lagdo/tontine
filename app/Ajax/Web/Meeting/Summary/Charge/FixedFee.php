<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

/**
 * @exclude
 */
class FixedFee extends CallableSessionClass
{
    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param FixedFeeService $feeService
     */
    public function __construct(protected LocaleService $localeService,
        protected FixedFeeService $feeService)
    {}

    public function show(SessionModel $session)
    {
        $this->session = $session;

        $html = $this->renderView('pages.meeting.summary.charge.fixed.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-fixed', $html);
        $this->jq('#btn-fees-fixed-refresh')->click($this->rq()->home());

        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);

        $html = $this->renderView('pages.meeting.summary.charge.fixed.page', [
            'session' => $this->session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
            'pagination' => '',
        ]);
        $this->response->html('meeting-fees-fixed-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-fees-fixed-page');

        return $this->response;
    }
}
