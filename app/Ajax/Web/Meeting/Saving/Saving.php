<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableSessionClass;
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
class Saving extends CallableSessionClass
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

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
        $html = $this->render('pages.meeting.saving.home', [
            'session' => $this->session,
            'fundId' => (int)$this->bag('meeting.saving')->get('fund.id', -1),
            'funds' => $this->fundService->getFundList()->prepend('', -1),
        ]);
        $this->response->html('meeting-savings', $html);

        $this->jq('#btn-savings-refresh')->click($this->rq()->home());
        $fundId = pm()->select('savings-fund-id')->toInt();
        $this->jq('#btn-savings-edit')->click($this->cl(Member::class)->rq()->home($fundId));
        $this->jq('#btn-savings-fund')->click($this->rq()->filter($fundId));

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $fundId = (int)$this->bag('meeting.saving')->get('fund.id', -1);
        $savingCount = $this->savingService->getSavingCount($this->session, $fundId);
        $savingSum = $this->savingService->getSavingSum($this->session, $fundId);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $savingCount,
            'meeting.saving', 'page');
        $savings = $this->savingService->getSavings($this->session, $fundId, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $savingCount);

        $html = $this->render('pages.meeting.saving.page', [
            'session' => $this->session,
            'savings' => $savings,
            'savingCount' => $savingCount,
            'savingSum' => $savingSum,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-savings-page', $html);

        $savingId = jq()->parent()->attr('data-saving-id')->toInt();
        $this->jq('.btn-saving-edit')->click($this->rq()->editSaving($savingId));
        $this->jq('.btn-saving-delete')->click($this->rq()->deleteSaving($savingId)
            ->confirm(trans('meeting.saving.questions.delete')));

        return $this->response;
    }

    public function filter(int $fundId)
    {
        $this->bag('meeting.saving')->set('fund.id', $fundId);

        return $this->page();
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
        $content = $this->render('pages.meeting.saving.edit', [
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
     */
    public function updateSaving(int $savingId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->savingService->updateSaving($this->session, $savingId, $values);

        $this->dialog->hide();

        return $this->home();
    }

    /**
     * @after showBalanceAmounts
     */
    public function deleteSaving(int $savingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->savingService->deleteSaving($this->session, $savingId);

        return $this->home();
    }
}
