<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\Cache;
use App\Ajax\MeetingComponent;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Service\Meeting\Credit\RefundService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function trans;

/**
 * @databag refund
 * @before getFund
 */
class Refund extends MeetingComponent
{
    /**
     * @var DebtValidator
     */
    protected DebtValidator $validator;

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

    public function html(): string
    {
        $session = Cache::get('meeting.session');

        return (string)$this->renderView('pages.meeting.refund.home', [
            'session' => $session,
            'funds' => $this->fundService->getFundList(),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->fund(0);
    }

    protected function getFund()
    {
        // Try to get the selected savings fund.
        // If not found, then revert to the tontine default fund.
        $fund = null;
        $fundId = $this->bag('refund')->get('fund.id', 0);
        if($fundId !== 0 && ($fund = $this->fundService->getFund($fundId, true)) === null)
        {
            $fundId = 0;
        }
        if($fundId === 0)
        {
            $fund = $this->fundService->getDefaultFund();
            $this->bag('refund')->set('fund.id', $fund->id);
        }
        Cache::set('meeting.refund.fund', $fund);
    }

    /**
     * @param int $fundId
     *
     * @return mixed
     */
    public function fund(int $fundId)
    {
        $this->bag('refund')->set('fund.id', $fundId);
        $this->bag('refund')->set('principal.page', 1);
        $this->getFund();

        return $this->cl(RefundPage::class)->page();
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
        $this->bag('refund')->set('principal.page', 1);

        return $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     * @di $validator
     */
    public function createRefund(string $debtId)
    {
        $debt = $this->refundService->getDebt($debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $session = Cache::get('meeting.session');
        $this->refundService->createRefund($debt, $session);

        return $this->cl(RefundPage::class)->page();
    }

    /**
     * @before getFund
     */
    public function deleteRefund(int $debtId)
    {
        $debt = $this->refundService->getDebt($debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $session = Cache::get('meeting.session');
        $this->refundService->deleteRefund($debt, $session);

        return $this->cl(RefundPage::class)->page();
    }
}
