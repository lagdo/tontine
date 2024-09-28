<?php

namespace App\Ajax\Web\Meeting\Session\Charge;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

use function Jaxon\jq;

class FixedFee extends OpenedSessionCallable
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

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    public function home()
    {
        $html = $this->renderView('pages.meeting.charge.fixed.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-fixed', $html);
        $this->response->jq('#btn-fees-fixed-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $chargeCount = $this->feeService->getFeeCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $chargeCount,
            'meeting', 'fee.fixed.page');
        $charges = $this->feeService->getFees($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $chargeCount);
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);

        $html = $this->renderView('pages.meeting.charge.fixed.page', [
            'session' => $this->session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
        ]);
        $this->response->html('meeting-fees-fixed-page', $html);
        $this->response->js()->makeTableResponsive('meeting-fees-fixed-page');

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->response->jq('.btn-fee-fixed-settlements')
            ->click($this->rq(Fixed\Settlement::class)->home($chargeId));

        return $this->response;
    }
}
