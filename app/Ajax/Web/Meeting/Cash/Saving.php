<?php

namespace App\Ajax\Web\Meeting\Cash;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Credit\Loan;
use Siak\Tontine\Service\Meeting\Cash\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
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
     * @var FundService
     */
    protected FundService $fundService;

    /**
     * @var SavingService
     */
    protected SavingService $savingService;

    /**
     * @var SavingValidator
     */
    protected SavingValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param SavingService $savingService
     */
    public function __construct(SavingService $savingService)
    {
        $this->savingService = $savingService;
    }

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
        $savings = $this->savingService->getSessionSavings($this->session);

        $html = $this->view()->render('tontine.pages.meeting.saving.home')
            ->with('session', $this->session)
            ->with('savings', $savings);
        $this->response->html('meeting-savings', $html);

        $this->jq('#btn-savings-refresh')->click($this->rq()->home());
        $this->jq('#btn-saving-add')->click($this->rq()->addSaving());
        $savingId = jq()->parent()->attr('data-saving-id')->toInt();
        $this->jq('.btn-saving-edit')->click($this->rq()->editSaving($savingId));
        $this->jq('.btn-saving-delete')->click($this->rq()->deleteSaving($savingId)
            ->confirm(trans('meeting.saving.questions.delete')));

        return $this->response;
    }

    /**
     * @di $fundService
     */
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
     * @di $validator
     */
    public function createSaving(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->savingService->createSaving($this->session, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    /**
     * @di $fundService
     */
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
     * @di $validator
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
}
