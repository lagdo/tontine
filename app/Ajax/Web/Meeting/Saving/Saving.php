<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Cash\Disbursement;
use App\Ajax\Web\Meeting\Credit\Loan;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Service\Tontine\MemberService;
use Siak\Tontine\Validation\Meeting\SavingValidator;

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
    protected SavingValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param SessionService $sessionService
     * @param SavingService $savingService
     * @param FundService $fundService
     * @param MemberService $memberService
     */
    public function __construct(protected SessionService $sessionService,
        protected SavingService $savingService, protected FundService $fundService,
        protected MemberService $memberService)
    {}

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->sessionService->getSession($sessionId);
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
        $html = $this->render('pages.meeting.saving.home', [
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

        $title = trans('meeting.saving.titles.add');
        $content = $this->render('pages.meeting.saving.add', [
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
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->savingService->createSaving($member, $this->session, $values);

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
        $content = $this->render('pages.meeting.saving.edit', [
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
        if(!($member = $this->memberService->getMember($values['member'])))
        {
            $this->notify->warning(trans('tontine.member.errors.not_found'));
            return $this->response;
        }

        $this->savingService->updateSaving($member, $this->session, $savingId, $values);

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
