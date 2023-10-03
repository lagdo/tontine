<?php

namespace App\Ajax\Web\Meeting\Cash;

use App\Ajax\CallableClass;
use App\Ajax\Web\Meeting\Credit\Loan;
use Siak\Tontine\Service\Meeting\Cash\FundingService;
use Siak\Tontine\Validation\Meeting\FundingValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Funding extends CallableClass
{
    /**
     * @var FundingService
     */
    protected FundingService $fundingService;

    /**
     * @var FundingValidator
     */
    protected FundingValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * The constructor
     *
     * @param FundingService $fundingService
     */
    public function __construct(FundingService $fundingService)
    {
        $this->fundingService = $fundingService;
    }

    /**
     * @return void
     */
    protected function getSession()
    {
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->fundingService->getSession($sessionId);
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
        $fundings = $this->fundingService->getSessionFundings($this->session);

        $html = $this->view()->render('tontine.pages.meeting.funding.home')
            ->with('session', $this->session)
            ->with('fundings', $fundings);
        $this->response->html('meeting-fundings', $html);

        $this->jq('#btn-fundings-refresh')->click($this->rq()->home());
        $this->jq('#btn-funding-add')->click($this->rq()->addFunding());
        $fundingId = jq()->parent()->attr('data-funding-id')->toInt();
        $this->jq('.btn-funding-edit')->click($this->rq()->editFunding($fundingId));
        $this->jq('.btn-funding-delete')->click($this->rq()->deleteFunding($fundingId)
            ->confirm(trans('meeting.funding.questions.delete')));

        return $this->response;
    }

    public function addFunding()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $members = $this->fundingService->getMembers();
        $title = trans('meeting.funding.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.funding.add')
            ->with('members', $members);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->createFunding(pm()->form('funding-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function createFunding(array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->fundingService->createFunding($this->session, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function editFunding(int $fundingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $funding = $this->fundingService->getSessionFunding($this->session, $fundingId);
        $title = trans('meeting.funding.titles.edit');
        $content = $this->view()->render('tontine.pages.meeting.funding.edit')
            ->with('funding', $funding);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->updateFunding($fundingId, pm()->form('funding-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function updateFunding(int $fundingId, array $formValues)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->fundingService->updateFunding($this->session, $fundingId, $values);

        $this->dialog->hide();

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }

    public function deleteFunding(int $fundingId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->fundingService->deleteFunding($this->session, $fundingId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->home();
    }
}
