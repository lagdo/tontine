<?php

namespace App\Ajax\App\Meeting\Financial;

use App\Ajax\App\Meeting\Pool;
use App\Ajax\CallableClass;
use Siak\Tontine\Service\Meeting\LoanService;
use Siak\Tontine\Service\Meeting\ReportService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Pool as PoolModel;

use function Jaxon\jq;
use function Jaxon\pm;

/**
 * @databag meeting
 * @before getPool
 */
class Remitment extends CallableClass
{
    /**
     * @di
     * @var LoanService
     */
    protected LoanService $loanService;

    /**
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * @var RemitmentValidator
     */
    protected RemitmentValidator $validator;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session = null;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool = null;

    /**
     * @return void
     */
    protected function getPool()
    {
        // Get session
        $sessionId = $this->bag('meeting')->get('session.id');
        $this->session = $this->loanService->getSession($sessionId);
        // Get pool
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->loanService->getPool($poolId);
        if($this->session->disabled($this->pool))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->pool = null;
        }
    }

    public function home($poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);

        $figures = $this->reportService->getRemitmentFigures($this->pool, $this->session->id);
        $payables = $figures->payables
            ->map(function($payable) use($figures) {
                return (object)[
                    'id' => $payable->subscription->id,
                    'title' => $payable->subscription->member->name,
                    'amount' => $figures->amount,
                    'paid' => 0,
                    'available' => false,
                ];
            })->pad($figures->count, (object)[
                'id' => 0,
                'title' => '** ' . trans('figures.loan.titles.available') . ' **',
                'amount' => $figures->amount,
                'paid' => 0,
                'available' => true,
            ]);

        $html = $this->view()->render('tontine.pages.meeting.remitment.financial')
            ->with('pool', $this->pool)
            ->with('session', $this->session)
            ->with('payables', $payables);
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-back')->click($this->cl(Pool::class)->rq()->remitments());
        $this->jq('.btn-add-remitment')->click($this->rq()->addRemitment());
        $subscriptionId = jq()->parent()->attr('data-subscription-id')->toInt();
        $this->jq('.btn-del-remitment')->click($this->rq()->deleteRemitment($subscriptionId));

        return $this->response;
    }

    public function addRemitment()
    {
        $members = $this->loanService->getSubscriptions($this->pool);
        $title = trans('tontine.loan.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.remitment.add')
            ->with('members', $members);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->saveRemitment(pm()->form('remitment-form')),
        ]];
        $this->dialog->show($title, $content, $buttons, ['width' => '800']);

        return $this->response;
    }

    /**
     * @di $validator
     */
    public function saveRemitment(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $this->loanService->createRemitment($this->pool, $this->session,
            $values['subscription'], $values['interest']);
        $this->dialog->hide();
        // $this->notify->success(trans('session.remitment.created'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }

    public function deleteRemitment(int $subscriptionId)
    {
        $this->loanService->deleteRemitment($this->pool, $this->session, $subscriptionId);
        // $this->notify->success(trans('session.remitment.deleted'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }
}
