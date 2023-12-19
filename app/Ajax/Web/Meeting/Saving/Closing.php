<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableClass;
use App\Ajax\Web\Report\Session\Profit;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\ClosingValidator;
use Siak\Tontine\Model\Session as SessionModel;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getSession
 */
class Closing extends CallableClass
{
    /**
     * @var ClosingValidator
     */
    protected ClosingValidator $validator;

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
        $html = $this->render('pages.meeting.closing.home', [
            'session' => $this->session,
            'closings' => $this->savingService->getSessionClosings($this->session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-closings', $html);

        $this->jq('#btn-closings-refresh')->click($this->rq()->home());
        $fundId = pm()->select('closings_fund_id')->toInt();
        $this->jq('#btn-closing-edit')->click($this->rq()->editClosing($fundId));
        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-closing-edit')->click($this->rq()->editClosing($fundId));
        $this->jq('.btn-profits-show')->click($this->rq()->showProfits($fundId));
        $this->jq('.btn-closing-delete')->click($this->rq()->deleteClosing($fundId)
            ->confirm(trans('meeting.closing.questions.delete')));

        return $this->response;
    }

    public function editClosing(int $fundId)
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

        $title = trans('meeting.saving.titles.closing', ['fund' => $funds[$fundId]]);
        $content = $this->render('pages.meeting.closing.edit', [
            'hasClosing' => $this->savingService->hasFundClosing($this->session, $fundId),
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveClosing(int $fundId, array $formValues)
    {
        $funds = $this->fundService->getFundList();
        if(!isset($funds[$fundId]))
        {
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
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

    public function showProfits(int $fundId)
    {
        return $this->cl(Profit::class)->show($this->session, $fundId);
    }
}
