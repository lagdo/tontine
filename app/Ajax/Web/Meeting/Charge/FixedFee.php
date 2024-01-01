<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\FixedFeeService;

use function Jaxon\jq;

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
        $html = $this->render('pages.meeting.charge.fixed.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-fixed', $html);
        $this->jq('#btn-fees-fixed-refresh')->click($this->rq()->home());

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

        $html = $this->render('pages.meeting.charge.fixed.page')
            ->with('session', $this->session)
            ->with('charges', $charges)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('pagination', $pagination);
        $this->response->html('meeting-fees-fixed-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-fee-fixed-settlements')
            ->click($this->cl(Fixed\Settlement::class)->rq()->home($chargeId));

        return $this->response;
    }
}
