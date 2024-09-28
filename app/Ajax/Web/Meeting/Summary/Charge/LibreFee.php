<?php

namespace App\Ajax\Web\Meeting\Summary\Charge;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

/**
 * @exclude
 */
class LibreFee extends SessionCallable
{
    /**
     * The constructor
     *
     * @param LocaleService $localeService
     * @param LibreFeeService $feeService
     */
    public function __construct(protected LocaleService $localeService,
        protected LibreFeeService $feeService)
    {}

    public function show(SessionModel $session)
    {
        $this->session = $session;

        $html = $this->renderView('pages.meeting.summary.charge.libre.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-libre', $html);
        $this->response->jq('#btn-fees-libre-refresh')->click($this->rq()->home());

        $charges = $this->feeService->getFees();
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);

        $html = $this->renderView('pages.meeting.summary.charge.libre.page', [
            'session' => $this->session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
            'pagination' => '',
        ]);
        $this->response->html('meeting-fees-libre-page', $html);
        $this->response->js()->makeTableResponsive('meeting-fees-libre-page');

        return $this->response;
    }
}
