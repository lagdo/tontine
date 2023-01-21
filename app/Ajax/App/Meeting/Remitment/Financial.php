<?php

namespace App\Ajax\App\Meeting\Remitment;

use App\Ajax\App\Meeting\Pool;
use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\RemitmentService;
use Siak\Tontine\Service\Meeting\ReportService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Financial extends CallableClass
{
    /**
     * @di
     * @var RemitmentService
     */
    protected RemitmentService $remitmentService;

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
        $this->session = $this->remitmentService->getSession($sessionId);
        // Get pool
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->remitmentService->getPool($poolId);
        if($this->session->disabled($this->pool))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->pool = null;
        }
    }

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function home(int $poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);

        $payables = $this->remitmentService->getPayables($this->pool, $this->session);
        $html = $this->view()->render('tontine.pages.meeting.remitment.financial', [
            'pool' => $this->pool,
            'payables' => $payables,
        ]);
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-back')->click($this->cl(Pool::class)->rq()->remitments());
        $this->jq('.btn-add-remitment')->click($this->rq()->addRemitment());
        $payableId = jq()->parent()->attr('data-payable-id')->toInt();
        $this->jq('.btn-del-remitment')->click($this->rq()->deleteRemitment($payableId));

        return $this->response;
    }

    /**
     * @return mixed
     */
    public function addRemitment()
    {
        $members = $this->remitmentService->getSubscriptions($this->pool);
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
     * @param array $formValues
     *
     * @return mixed
     */
    public function saveRemitment(array $formValues)
    {
        $values = $this->validator->validateItem($formValues);

        $this->remitmentService->saveFinancialRemitment($this->pool,
            $this->session, $values['payable'], $values['amount']);
        $this->dialog->hide();
        // $this->notify->success(trans('session.remitment.created'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function deleteRemitment(int $subscriptionId)
    {
        $this->remitmentService->deleteFinancialRemitment($this->pool, $this->session, $subscriptionId);
        // $this->notify->success(trans('session.remitment.deleted'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }
}
