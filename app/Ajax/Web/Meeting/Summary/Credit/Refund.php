<?php

namespace App\Ajax\Web\Meeting\Summary\Credit;

use App\Ajax\CallableSessionClass;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;

use function Jaxon\pm;

/**
 * @databag refund
 */
class Refund extends CallableSessionClass
{
    /**
     * @var FundModel|null
     */
    private $fund = null;

    /**
     * The constructor
     *
     * @param FundService $fundService
     * @param RefundService $refundService
     */
    public function __construct(protected FundService $fundService,
        protected RefundService $refundService)
    {}

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        $html = $this->renderView('pages.meeting.summary.refund.home', [
            'session' => $this->session,
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-refunds', $html);

        $this->jq('#btn-refunds-filter')->click($this->rq()->toggleFilter());
        $fundId = pm()->select('refunds-fund-id')->toInt();
        $this->jq('#btn-refunds-fund')->click($this->rq()->fund($fundId));

        return $this->fund(0);
    }

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
        $fundId = $this->bag('refund')->get('fund.id', 0);
        if($fundId !== 0 && ($this->fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $this->fund = $this->fundService->getDefaultFund();
            $this->bag('refund')->set('fund.id', $this->fund->id);
        }
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('refund')->set('fund.id', $fundId);
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
        $filtered = $this->bag('refund')->get('filter', null);

        $debtCount = $this->refundService->getDebtCount($this->session, $this->fund, $filtered);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $debtCount, 'refund', 'principal.page');
        $debts = $this->refundService->getDebts($this->session, $this->fund, $filtered, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $debtCount);

        $html = $this->renderView('pages.meeting.summary.refund.page', [
            'session' => $this->session,
            'debts' => $debts,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-debts-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-debts-page');

        return $this->response;
    }

    /**
     * @before getFund
     */
    public function toggleFilter()
    {
        $filtered = $this->bag('refund')->get('filter', null);
        // Switch between null, true and false
        $filtered = $filtered === null ? true : ($filtered === true ? false : null);
        $this->bag('refund')->set('filter', $filtered);

        return $this->page(1);
    }
}
