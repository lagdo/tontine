<?php

namespace App\Ajax\App\Meeting\Pool\Remitment;

use App\Ajax\CallableClass;
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
class Mutual extends CallableClass
{
    /**
     * @di
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
     */
    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * @return void
     */
    protected function getPool()
    {
        $sessionId = $this->bag('meeting')->get('session.id');

        // No pool id on the "home" page
        if($this->target()->method() === 'home')
        {
            $this->session = $this->poolService->getSession($sessionId);
            return;
        }

        $this->session = $this->remitmentService->getSession($sessionId);
        $poolId = $this->target()->method() === 'pool' ?
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
    public function show(SessionModel $session)
    {
        $this->session = $session;

        return $this->home();
    }

    /**
     * @di $poolService
     */
    public function home()
    {
        $tontine = $this->poolService->getTontine();
        $html = $this->view()->render('tontine.pages.meeting.remitment.home')
            ->with('tontine', $tontine)
            ->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithPayables($this->session));
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-remitments')->click($this->rq()->pool($poolId));

        return $this->response;
    }

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function pool(int $poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);

        $html = $this->view()->render('tontine.pages.meeting.remitment.pool', [
            'pool' => $this->pool,
        ]);
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-back')->click($this->rq()->home());

        return $this->page();
    }

    public function page()
    {
        $html = $this->view()->render('tontine.pages.meeting.remitment.mutual', [
            'session' => $this->session,
            'payables' => $this->remitmentService->getPayables($this->pool, $this->session),
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
        // $this->notify->success(trans('session.remitment.created'), trans('common.titles.success'));

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
        // $this->notify->success(trans('session.remitment.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
