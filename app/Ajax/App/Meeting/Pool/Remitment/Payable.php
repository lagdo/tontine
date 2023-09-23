<?php

namespace App\Ajax\App\Meeting\Pool\Remitment;

use App\Ajax\CallableClass;
use App\Ajax\App\Meeting\Cash\Disbursement;
use App\Ajax\App\Meeting\Credit\Loan;
use App\Ajax\App\Meeting\Pool\Remitment;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
use Siak\Tontine\Service\Meeting\Pool\RemitmentService;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Payable extends CallableClass
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
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

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
        $html = $this->view()->render('tontine.pages.meeting.remitment.payable', [
            'session' => $this->session,
            'payables' => $this->pool->deposit_fixed ?
                $this->remitmentService->getPayables($this->pool, $this->session) :
                $this->remitmentService->getLibrePayables($this->pool, $this->session),
        ]);
        $this->response->html('meeting-pool-remitments', $html);

        $payableId = jq()->parent()->attr('data-payable-id')->toInt();
        $this->jq('.btn-add-remitment')->click($this->rq()->saveRemitment($payableId));
        $this->jq('.btn-del-remitment')->click($this->rq()->deleteRemitment($payableId));

        return $this->response;
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function saveRemitment(int $payableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->remitmentService->saveMutualRemitment($this->pool, $this->session, $payableId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

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

        $this->remitmentService->deleteMutualRemitment($this->pool, $this->session, $payableId);

        // Refresh the amounts available
        $this->cl(Loan::class)->refreshAmount($this->session);
        $this->cl(Disbursement::class)->refreshAmount($this->session);

        return $this->page();
    }
}
