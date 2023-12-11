<?php

namespace App\Ajax\Web\Meeting\Cash;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Credit\Loan;
use Siak\Tontine\Service\Meeting\Cash\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\ClosingValidator;
use Siak\Tontine\Validation\Meeting\SavingValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Saving extends CallableClass
{
    /**
     * @var SavingValidator
     */
    protected SavingValidator $savingValidator;

    /**
     * @var ClosingValidator
     */
    protected ClosingValidator $closingValidator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     * @param FundService $fundService
     */
    public function __construct(protected SavingService $savingService,
        protected FundService $fundService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->savingService->getSession($sessionId);
    }

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
        $html = $this->view()->render('tontine.pages.meeting.saving.home', [
            'session' => $this->session,
            'savings' => $this->savingService->getSessionSavings($this->session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-savings', $html);

        $this->jq('#btn-savings-refresh')->click($this->rq()->home());
        $this->jq('#btn-saving-add')->click($this->rq()->addSaving());
        $savingId = jq()->parent()->attr('data-saving-id')->toInt();
        $this->jq('.btn-saving-edit')->click($this->rq()->editSaving($savingId));
        $this->jq('.btn-saving-delete')->click($this->rq()->deleteSaving($savingId)
            ->confirm(trans('meeting.saving.questions.delete')));
        $fundId = pm()->select('savings_fund_id')->toInt();
        $this->jq('#btn-savings-closing')->click($this->rq()->showClosing($fundId));

        return $this->response;
    }

    public function addSaving()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $members = $this->savingService->getMembers();
        $title = trans('meeting.saving.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.saving.add', [
            'members' => $members,
            'funds' => $this->fundService->getFundList(),
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createSaving(pm()->form('saving-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $savingValidator
     */
    public function createSaving(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->savingValidator->validateItem($formValues);
        $this->savingService->createSaving($this->session, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function editSaving(int $savingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $saving = $this->savingService->getSessionSaving($this->session, $savingId);
        $title = trans('meeting.saving.titles.edit');
        $content = $this->view()->render('tontine.pages.meeting.saving.edit', [
            'saving' => $saving,
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
     * @di $savingValidator
     */
    public function updateSaving(int $savingId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->savingValidator->validateItem($formValues);
        $this->savingService->updateSaving($this->session, $savingId, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function deleteSaving(int $savingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->savingService->deleteSaving($this->session, $savingId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function showClosing(int $fundId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $hasClosing = $this->savingService->hasFundClosing($this->session, $fundId);
        $title = trans('meeting.saving.titles.closing', ['fund' => $funds[$fundId]]);
        $content = $this->view()->render('tontine.pages.meeting.saving.closing', [
            'hasClosing' => $hasClosing,
            'profitAmount' => $this->savingService->getProfitAmount($this->session, $fundId),
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveClosing($fundId, pm()->form('closing-form')),
        ]];
        if($hasClosing)
        {
            $buttons[] = [
                'title' => trans('common.actions.delete'),
                'class' => 'btn btn-danger',
                'click' => $this->rq()->deleteClosing($fundId),
            ];
        }
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $closingValidator
     */
    public function saveClosing(int $fundId, array $formValues)
    {
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $values = $this->closingValidator->validateItem($formValues);
        $this->savingService->saveFundClosing($this->session, $fundId, $values['amount']);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->home();
    }

    public function deleteClosing(int $fundId)
    {
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $this->savingService->deleteFundClosing($this->session, $fundId);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->home();
    }
}
