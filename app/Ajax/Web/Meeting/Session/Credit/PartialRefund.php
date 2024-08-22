<?php

namespace App\Ajax\Web\Meeting\Session\Credit;

use App\Ajax\OpenedSessionCallable;
use Siak\Tontine\Model\Fund as FundModel; 
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Credit\PartialRefundService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\DebtValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag partial.refund
 */
class PartialRefund extends OpenedSessionCallable
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

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

        return $this->home();
    }

    public function home()
    {
        $html = $this->renderView('pages.meeting.refund.partial.home', [
            'session' => $this->session,
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-partial-refunds', $html);

        $this->jq('#btn-partial-refunds-refresh')->click($this->rq()->home());
        $this->jq('#btn-partial-refunds-edit')->click($this->rq()->editRefunds());
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

        $html = $this->renderView('pages.meeting.refund.partial.page', [
            'session' => $this->session,
            'refunds' => $refunds,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-partial-refunds-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-partial-refunds-page');

        $refundId = jq()->parent()->attr('data-partial-refund-id')->toInt();
        $this->jq('.btn-partial-refund-edit')->click($this->rq()->editRefund($refundId));
        $this->jq('.btn-partial-refund-delete')->click($this->rq()->deleteRefund($refundId)
            ->confirm(trans('meeting.refund.questions.delete')));
        $this->jq('.btn-del-partial-refund')->click($this->rq()->deleteRefund($refundId)
            ->confirm(trans('meeting.refund.questions.delete')));

        return $this->response;
    }

    /**
     * @before getFund
     */
    public function editRefunds()
    {
        $html = $this->renderView('pages.meeting.refund.partial.edit-list', [
            'session' => $this->session,
            'debts' => $this->refundService->getUnpaidDebts($this->fund, $this->session),
        ]);
        $this->response->html('meeting-partial-refunds', $html);

        $this->jq('#btn-partial-refunds-back')->click($this->rq()->home());
        $this->jq('#btn-partial-refunds-refresh')->click($this->rq()->editRefunds());
        $debtId = jq()->parent()->attr('data-debt-id')->toInt();
        $this->jq('.btn-partial-refund-edit-amount')->click($this->rq()->editAmount($debtId));
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-partial-refund-save-amount')->click($this->rq()->saveAmount($debtId, $amount));

        return $this->response;
    }

    /**
     * @before getFund
     * @di $localeService
     */
    public function editAmount(int $debtId)
    {
        $debt = $this->refundService->getUnpaidDebt($this->fund, $this->session, $debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $html = $this->renderView('pages.meeting.refund.partial.amount.edit', [
            'debt' => $debt,
            'amount' => $this->localeService->getMoneyValue($debt->partial_refund->amount),
        ]);
        $parentDiv = "partial-refund-amount-{$debt->id}";
        $this->response->html($parentDiv, $html);
        $debtId = jq()->parent()->attr('data-debt-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-partial-refund-save-amount', "#$parentDiv")
            ->click($this->rq()->saveAmount($debtId, $amount));

        return $this->response;
    }

    /**
     * @before getFund
     * @di $validator
     * @after showBalanceAmounts
     */
    public function saveAmount(int $debtId, int $amount)
    {
        $values = $this->validator->validateItem(['debt' => $debtId, 'amount' => $amount]);
        $debt = $this->refundService->getUnpaidDebt($this->fund, $this->session, $debtId);
        if(!$debt)
        {
            $this->notify->warning(trans('meeting.loan.errors.not_found'));
            return $this->response;
        }

        $this->refundService->savePartialRefund($debt, $this->session, $values['amount']);

        $this->dialog->hide();

        // Refresh the refunds page
        $this->cl(Refund::class)->show($this->session);

        return $this->editRefunds();
    }

    public function editRefund(int $refundId)
    {
        $refund = $this->refundService->getPartialRefund($this->session, $refundId);
        $title = trans('meeting.refund.titles.edit');
        $content = $this->renderView('pages.meeting.refund.partial.edit', [
            'session' => $this->session,
            'refund' => $refund,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateRefund($refundId, pm()->form('refund-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @before getFund
     * @di $validator
     * @after showBalanceAmounts
     */
    public function updateRefund(int $refundId, array $formValues)
    {
        $formValues['debt'] = $refundId;
        $values = $this->validator->validateItem($formValues);
        $refund = $this->refundService->getPartialRefund($this->session, $refundId);

        $this->refundService->updatePartialRefund($refund, $this->session, $values['amount']);

        $this->dialog->hide();

        // Refresh the refunds page
        $this->cl(Refund::class)->show($this->session);

        return $this->page();
    }

    /**
     * @before getFund
     * @after showBalanceAmounts
     */
    public function deleteRefund(int $refundId)
    {
        $refund = $this->refundService->getPartialRefund($this->session, $refundId);
        $this->refundService->deletePartialRefund($refund, $this->session);

        // Refresh the refunds page
        $this->cl(Refund::class)->show($this->session);

        return $this->page();
    }
}
