<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\SessionCallable;
use Siak\Tontine\Model\Fund as FundModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting.saving
 */
class Saving extends SessionCallable
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

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

        return $this->home();
    }

    public function home()
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', 0);
        $html = $this->renderView('pages.meeting.saving.home', [
            'session' => $this->session,
            'fundId' => $fundId,
            'funds' => $this->fundService->getFundList()->prepend('', 0),
        ]);
        $this->response->html('meeting-savings', $html);

        $this->jq('#btn-savings-refresh')->click($this->rq()->home());
        $selectFundId = pm()->select('savings-fund-id')->toInt();
        $this->jq('#btn-savings-edit')->click($this->rq(Member::class)
            ->home($selectFundId)->ifgt($selectFundId, 0));
        $this->jq('#btn-savings-fund')->click($this->rq()->fund($selectFundId));

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
        $html = $this->renderView('pages.meeting.saving.total', [
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

        $html = $this->renderView('pages.meeting.saving.page', [
            'session' => $this->session,
            'savings' => $savings,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-savings-page', $html);
        $this->response->call('makeTableResponsive', 'meeting-savings-page');

        $savingId = jq()->parent()->attr('data-saving-id')->toInt();
        $this->jq('.btn-saving-edit')->click($this->rq()->editSaving($savingId));
        $this->jq('.btn-saving-delete')->click($this->rq()->deleteSaving($savingId)
            ->confirm(trans('meeting.saving.questions.delete')));

        return $this->response;
    }

    public function editSaving(int $savingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $saving = $this->savingService->getSaving($this->session, $savingId);
        $title = trans('meeting.saving.titles.edit');
        $content = $this->renderView('pages.meeting.saving.edit', [
            'saving' => $saving,
            'members' => $this->memberService->getMemberList(),
            'funds' => $this->fundService->getFundList(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateSaving($savingId, pm()->form('saving-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     * @after showBalanceAmounts
     * @before getFund
     */
    public function updateSaving(int $savingId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if(!($saving = $this->savingService->getSaving($this->session, $savingId)))
        {
            $this->notify->warning(trans('meeting.saving.errors.not_found'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        if(!($member = $this->savingService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }
        if(!($fund = $this->fundService->getFund($values['fund'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $amount = $values['amount'];
        $this->savingService->updateSaving($this->session, $fund, $member, $saving, $amount);

        $this->dialog->hide();

        return $this->page();
    }

    /**
     * @after showBalanceAmounts
     * @before getFund
     */
    public function deleteSaving(int $savingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->savingService->deleteSaving($this->session, $savingId);

        return $this->page();
    }
}
