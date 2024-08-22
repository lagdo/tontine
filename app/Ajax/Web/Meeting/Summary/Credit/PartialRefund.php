<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Fund as FundModel; 
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;

/**
 * @databag partial.refund
 */
class PartialRefund extends CallableSessionClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var FundModel|null
     */
    private $fund = null;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param PartialRefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected PartialRefundService $refundService)
    {}

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        $html = $this->renderView('pages.meeting.summary.refund.partial.home', [
            'session' => $this->session,
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-partial-refunds', $html);

        $fundId = pm()->select('partial-refunds-fund-id')->toInt();
        $this->jq('#btn-partial-refunds-fund')->click($this->rq()->fund($fundId));

        return $this->fund(0);
    }

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
        $fundId = $this->bag('partial.refund')->get('fund.id', 0);
        if($fundId !== 0 && ($this->fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $this->fund = $this->fundService->getDefaultFund();
            $this->bag('partial.refund')->set('fund.id', $this->fund->id);
        }
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('partial.refund')->set('fund.id', $fundId);
        $this->getFund();

        return $this->page(0);
    }

    /**
     * @before getFund
     *
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $refundCount = $this->refundService->getPartialRefundCount($this->session, $this->fund);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $refundCount,
            'partial.refund', 'principal.page');
        $refunds = $this->refundService->getPartialRefunds($this->session, $this->fund, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $refundCount);

        $html = $this->renderView('pages.meeting.summary.refund.partial.page', [
            'session' => $this->session,
            'refunds' => $refunds,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-partial-refunds-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-partial-refunds-page');

        return $this->response;
    }
}
