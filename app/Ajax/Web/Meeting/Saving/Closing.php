<?php

namespace App\Ajax\Web\Meeting\Saving;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Report\Session\Saving;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
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
     * @param ClosingService $closingService
     * @param FundService $fundService
     */
    public function __construct(protected ClosingService $closingService,
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
            'closings' => $this->closingService->getClosings($this->session),
            'funds' => $this->fundService->getFundList(),
        ]);
        $this->response->html('meeting-closings', $html);
        $this->response->call('makeTableResponsive', 'meeting-closings');

        // Sending an Ajax request to the Saving class needs to set
        // the session id in the report databag.
        $this->bag('report')->set('session.id', $this->session->id);

        $this->jq('#btn-closings-refresh')->click($this->rq()->home());

        $selectFundId = pm()->select('closings-fund-id')->toInt();
        $this->jq('#btn-fund-edit-round-closing')
            ->click($this->rq()->editRoundClosing($selectFundId));
        $this->jq('#btn-fund-edit-interest-closing')
            ->click($this->rq()->editInterestClosing($selectFundId));
        $this->jq('#btn-fund-show-savings')->click($this->rq()->showSavings($selectFundId));

        $selectFundId = jq()->parent()->attr('data-fund-id')->toInt();
        $this->jq('.btn-fund-edit-round-closing')
            ->click($this->rq()->editRoundClosing($selectFundId));
        $this->jq('.btn-fund-edit-interest-closing')
            ->click($this->rq()->editInterestClosing($selectFundId));
        $this->jq('.btn-fund-delete-round-closing')
            ->click($this->rq()->deleteRoundClosing($selectFundId)
                ->confirm(trans('meeting.closing.questions.delete')));
        $this->jq('.btn-fund-delete-interest-closing')
            ->click($this->rq()->deleteInterestClosing($selectFundId)
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

    public function editRoundClosing(int $fundId)
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

        $title = trans('meeting.closing.titles.round');
        $content = $this->render('pages.meeting.closing.round', [
            'fund' => $fund,
            'closing' => $this->closingService->getRoundClosing($this->session, $fund),
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRoundClosing($fundId, pm()->form('closing-form')),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveRoundClosing(int $fundId, array $formValues)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $values = $this->validator->validateItem($formValues);
        $this->closingService->saveRoundClosing($this->session, $fund, $values['amount']);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->home();
    }

    public function deleteRoundClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->closingService->deleteRoundClosing($this->session, $fund);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.deleted'), trans('common.titles.success'));

        return $this->home();
    }

    public function editInterestClosing(int $fundId)
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

        $title = trans('meeting.closing.titles.interest');
        $content = $this->render('pages.meeting.closing.interest', [
            'fund' => $fund,
            'closing' => $this->closingService->getInterestClosing($this->session, $fund),
        ]);
        $buttons = [[
            'title' => trans('common.actions.close'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ], [
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveInterestClosing($fundId),
        ]];
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveInterestClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->closingService->saveInterestClosing($this->session, $fund);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.saved'), trans('common.titles.success'));

        return $this->home();
    }

    public function deleteInterestClosing(int $fundId)
    {
        $fund = $this->fundService->getFund($fundId, true, true);
        if(!$fund)
        {
            return $this->response;
        }

        $this->closingService->deleteInterestClosing($this->session, $fund);

        $this->dialog->hide();
        $this->notify->success(trans('meeting.messages.profit.deleted'), trans('common.titles.success'));

        return $this->home();
    }
}
