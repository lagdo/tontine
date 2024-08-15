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
use function Jaxon\rq;
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
        $this->response->call('makeTableResponsive', 'meeting-closings');

        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $this->session->id);

        $this->jq('#btn-closings-refresh')->click($this->rq()->home());

        $fundId = pm()->select('closings-fund-id')->toInt();
        $this->jq('#btn-fund-edit-closing')->click($this->rq()->editClosing($fundId));
        $this->jq('#btn-fund-show-savings')->click($this->rq()->showSavings($fundId));

        $fundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-edit-closing')->click($this->rq()->editClosing($fundId));
        $this->jq('.btn-fund-show-savings')->click($this->rq()->showSavings($fundId));
        $this->jq('.btn-closing-delete')->click($this->rq()->deleteClosing($fundId)
            ->confirm(trans('meeting.closing.questions.delete')));

        return $this->response;
    }

    /**
     * @databag report
     */
    public function showSavings(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->cl(Saving::class)->show($this->session, $fund);

        $this->response->call('showSmScreen', 'report-fund-savings', 'session-savings');
        $this->jq('#btn-presence-sessions-back')->click(rq('.')
            ->showSmScreen('meeting-closings', 'session-savings'));

        return $this->response;
    }

    public function editClosing(int $fundId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $closing = $this->closingService->getSavingsClosing($this->session, $fund);
        $title = trans('meeting.saving.titles.closing', ['fund' => $fund]);
        $content = $this->render('pages.meeting.closing.edit', [
            'hasClosing' => $closing !== null,
            'profitAmount' => $closing?->profit ?? 0,
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
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->closingService->saveFundClosing($this->session, $fund, $values['amount']);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->home();
    }

    public function deleteClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->closingService->deleteFundClosing($this->session, $fund);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.deleted'), trans('common.titles.success'));

        return $this->home();
    }
}
