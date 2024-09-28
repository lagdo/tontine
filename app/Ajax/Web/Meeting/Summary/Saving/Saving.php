<?php

namespace App\Ajax\Web\Meeting\Summary\Saving;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;

use function Jaxon\pm;

/**
 * @databag meeting.saving
 */
class Saving extends SessionCallable
{
    /**
     * @var FundModel|null
     */
    protected ?FundModel $fund = null;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(protected SavingService $savingService,
        protected FundService $fundService, protected MemberService $memberService)
    {}

    /**
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $html = $this->renderView('pages.meeting.summary.saving.home', [
            'session' => $this->session,
            'fundId' => $fundId,
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
        $this->response->html('meeting-savings', $html);

        $selectFundId = pm()->select('savings-fund-id')->toInt();
        $this->response->jq('#btn-savings-fund')->click($this->rq()->fund($selectFundId));

        return $this->fund($fundId);
    }

    protected function getFund()
    {
        $fundId = $this->bag('meeting.saving')->get('fund.id', 0);
        if($fundId > 0 && ($this->fund = $this->fundService->getFund($fundId, true, true)) === null)
        {
            $this->bag('meeting.saving')->set('fund.id', 0);
        }
    }

    public function fund(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);
        $this->getFund();

        return $this->page();
    }

    private function showTotal(int $savingCount)
    {
        $savingTotal = $this->savingService->getSavingTotal($this->session, $this->fund);
        $html = $this->renderView('pages.meeting.summary.saving.total', [
            'savingCount' => $savingCount,
            'savingTotal' => $savingTotal,
        ]);
        $this->response->html('meeting-savings-total', $html);
    }

    /**
     * @before getFund
     */
    public function page(int $pageNumber = 0)
    {
        $savingCount = $this->savingService->getSavingCount($this->session, $this->fund);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $savingCount,
            'meeting.saving', 'page');
        $savings = $this->savingService->getSavings($this->session, $this->fund, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $savingCount);

        $this->showTotal($savingCount);

        $html = $this->renderView('pages.meeting.summary.saving.page', [
            'session' => $this->session,
            'savings' => $savings,
        ]);
        $this->response->html('meeting-savings-page', $html);
        $this->response->js()->makeTableResponsive('meeting-savings-page');

        return $this->response;
    }
}
