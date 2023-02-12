<?php

namespace App\Ajax\App\Meeting\Credit;

use Siak\Tontine\Service\Meeting\FundingService;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Validation\Meeting\FundingValidator;
use Siak\Tontine\Model\Session as SessionModel;
use App\Ajax\CallableClass;

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
     * @di
     * @var FundingService
     */
    protected FundingService $fundingService;

    /**
     * @var FundingValidator
     */
    protected FundingValidator $validator;

    /**
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

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
    public function show(SessionModel $session, FundingService $fundingService)
    {
        $this->session = $session;
        $this->fundingService = $fundingService;

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
        $this->jq('.btn-funding-delete')->click($this->rq()->deleteFunding($fundingId)
            ->confirm(trans('tontine.funding.questions.delete')));

        return $this->response;
    }

    public function addFunding()
    {
        $members = $this->fundingService->getMembers();
        $title = trans('tontine.funding.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.funding.add')
            ->with('members', $members);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveFunding(pm()->form('funding-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $validator
     * @di $loanService
     */
    public function saveFunding(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $memberId = $values['member'];
        $amount = $values['amount'];
        $this->fundingService->createFunding($this->session, $memberId, $amount);

        $this->dialog->hide();

        // Refresh the loans page
        $this->cl(Loan::class)->show($this->session, $this->loanService);

        return $this->home();
    }

    /**
     * @di $loanService
     */
    public function deleteFunding(int $fundingId)
    {
        $this->fundingService->deleteFunding($this->session, $fundingId);

        // Refresh the loans page
        $this->cl(Loan::class)->show($this->session, $this->loanService);

        return $this->home();
    }
}
