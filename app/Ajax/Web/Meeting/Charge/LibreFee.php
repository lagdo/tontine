<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

use function Jaxon\jq;

class LibreFee extends CallableSessionClass
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
        $html = $this->render('pages.meeting.charge.libre.home')
            ->with('session', $this->session);
        $this->response->html('meeting-fees-libre', $html);
        $this->jq('#btn-fees-libre-refresh')->click($this->rq()->home());

        return $this->page(1);
    }

    public function page(int $pageNumber)
    {
        $chargeCount = $this->feeService->getFeeCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $chargeCount,
            'meeting', 'fee.libre.page');
        $charges = $this->feeService->getFees($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $chargeCount);
        // Bill and settlement counts and amounts
        $bills = $this->feeService->getBills($this->session);
        $settlements = $this->feeService->getSettlements($this->session);

        $html = $this->render('pages.meeting.charge.libre.page')
            ->with('session', $this->session)
            ->with('charges', $charges)
            ->with('bills', $bills)
            ->with('settlements', $settlements)
            ->with('pagination', $pagination);
        $this->response->html('meeting-fees-libre-page', $html);

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-fee-libre-add')
            ->click($this->cl(Libre\Member::class)->rq()->home($chargeId));
        $this->jq('.btn-fee-libre-settlements')
            ->click($this->cl(Libre\Settlement::class)->rq()->home($chargeId));
        $this->jq('.btn-fee-libre-target')
            ->click($this->cl(Libre\Target::class)->rq()->home($chargeId));

        return $this->response;
    }
}
