<?php

namespace App\Ajax\Web\Meeting\Charge;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Charge\LibreFeeService;

use function Jaxon\jq;

class LibreFee extends OpenedSessionCallable
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
        $html = $this->renderView('pages.meeting.charge.libre.home')
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

        $html = $this->renderView('pages.meeting.charge.libre.page', [
            'session' => $this->session,
            'charges' => $charges,
            'bills' => $bills,
            'settlements' => $settlements,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-fees-libre-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-fees-libre-page');

        $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
        $this->jq('.btn-fee-libre-add')
            ->click($this->rq(Libre\Member::class)->home($chargeId));
        $this->jq('.btn-fee-libre-settlements')
            ->click($this->rq(Libre\Settlement::class)->home($chargeId));
        $this->jq('.btn-fee-libre-target')
            ->click($this->rq(Libre\Target::class)->home($chargeId));

        return $this->response;
    }
}
