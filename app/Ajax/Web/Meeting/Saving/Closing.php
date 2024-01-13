<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Report\Session\Saving;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Tontine\FundService;
use Siak\Tontine\Validation\Meeting\ClosingValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

class Closing extends CallableSessionClass
{
    /**
     * @var ClosingValidator
     */
    protected ClosingValidator $validator;

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
     * @exclude
     */
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    /**
     * @databag report
     */
    public function home()
    {
        $html = $this->render('pages.meeting.closing.home', [
            'session' => $this->session,
            'closings' => $this->savingService->getSessionClosings($this->session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-closings', $html);

        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $this->session->id);

        $this->jq('#btn-closings-refresh')->click($this->rq()->home());

        $fundId = pm()->select('closings-fund-id')->toInt();
        $this->jq('#btn-closing-edit')->click($this->rq()->editClosing($fundId));
        $this->jq('#btn-fund-savings-show')->click($this->cl(Saving::class)->rq()->home($fundId));

        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-closing-edit')->click($this->rq()->editClosing($fundId));
        $this->jq('.btn-fund-savings-show')->click($this->cl(Saving::class)->rq()->home($fundId));
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
        $this->notify->success(trans('meeting.messages.profit.deleted'), trans('common.titles.success'));

        return $this->home();
    }
}
