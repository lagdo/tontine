<?php

namespace App\Ajax\App\Meeting\Pool\Remitment;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Credit\Loan;
use App\Ajax\App\Meeting\Credit\Refund;
use App\Ajax\App\Meeting\Pool\Auction;
use App\Ajax\App\Meeting\Pool\Remitment;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;
use Siak\Tontine\Validation\Meeting\RemitmentValidator;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Subscription extends CallableClass
{
    /**
     * @var RemitmentService
     */
    protected RemitmentService $remitmentService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

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
     * The constructor
     *
     * @param PoolService $poolService
     * @param RemitmentService $remitmentService
     */
    public function __construct(PoolService $poolService, RemitmentService $remitmentService)
    {
        $this->poolService = $poolService;
        $this->remitmentService = $remitmentService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $sessionId = $this->bag('meeting')->get('session.id');

        $this->session = $this->remitmentService->getSession($sessionId);
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->remitmentService->getPool($poolId);
        if(!$this->session || !$this->pool || $this->session->disabled($this->pool))
        {
            $this->notify->error(trans('tontine.session.errors.disabled'), trans('common.titles.error'));
            $this->pool = null;
        }
    }

    /**
     * @exclude
     */
    public function show(SessionModel $session, PoolModel $pool)
    {
        $this->session = $session;
        $this->pool = $pool;

        return $this->home($pool->id);
    }

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function home(int $poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);

        $html = $this->view()->render('tontine.pages.meeting.remitment.pool', [
            'pool' => $this->pool,
        ]);
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-back')->click($this->cl(Remitment::class)->rq()->home());

        return $this->page();
    }

    public function page()
    {
        $html = $this->view()->render('tontine.pages.meeting.remitment.subscription', [
            'session' => $this->session,
            'payables' => $this->pool->deposit_fixed ?
                $this->remitmentService->getPayables($this->pool, $this->session) :
                $this->remitmentService->getLibrePayables($this->pool, $this->session),
        ]);
        $this->response->html('meeting-pool-remitments', $html);

        $payableId = jq()->parent()->attr('data-payable-id')->toInt();
        $this->jq('#btn-add-remitment')->click($this->rq()->addRemitment());
        $this->jq('.btn-del-remitment')->click($this->rq()->deleteRemitment($payableId));

        return $this->response;
    }

    public function addRemitment()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $members = $this->remitmentService->getSubscriptions($this->pool);
        $title = trans('meeting.remitment.titles.add');
        $content = $this->view()->render('tontine.pages.meeting.remitment.add')
            ->with('pool', $this->pool)
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
        $this->dialog->show($title, $content, $buttons);

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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            $this->dialog->hide();
            return $this->response;
        }

        // Add some data in the input values to help validation.
        $formValues['remit_amount'] = $this->pool->remit_fixed ? 0 : 1;
        $formValues['remit_auction'] = $this->pool->remit_auction ? 1 : 0;

        $values = $this->validator->validateItem($formValues);
        $this->remitmentService->saveFinancialRemitment($this->pool,
            $this->session, $values['payable'], $values['amount'], $values['auction']);
        $this->dialog->hide();
        // $this->notify->success(trans('session.remitment.created'), trans('common.titles.success'));

        // Refresh the auction page
        $this->cl(Auction::class)->show($this->session);
        // Refresh the refunds pages
        $this->cl(Loan::class)->show($this->session);
        $this->cl(Refund::class)->show($this->session);

        return $this->page();
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function deleteRemitment(int $payableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->remitmentService->deleteFinancialRemitment($this->pool, $this->session, $payableId);
        // $this->notify->success(trans('session.remitment.deleted'), trans('common.titles.success'));

        // Refresh the auction page
        $this->cl(Auction::class)->show($this->session);
        // Refresh the refunds pages
        $this->cl(Loan::class)->show($this->session);
        $this->cl(Refund::class)->show($this->session);

        return $this->page();
    }
}
