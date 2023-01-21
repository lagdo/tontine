<?php

namespace App\Ajax\App\Meeting\Remitment;

use App\Ajax\App\Meeting\Pool;
use App\Ajax\CallableClass;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Service\Meeting\RemitmentService;

use function intval;
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
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

    /**
     * @return void
     */
    protected function getPool()
    {
        // Get session
        $sessionId = intval($this->bag('meeting')->get('session.id'));
        $this->session = $this->remitmentService->getSession($sessionId);
        // Get pool
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : intval($this->bag('meeting')->get('pool.id'));
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
        $html = $this->view()->render('tontine.pages.meeting.remitment.mutual', [
            'pool' => $this->pool,
            'payables' => $payables,
        ]);
        $this->response->html('meeting-remitments', $html);

        $this->jq('#btn-remitments-back')->click($this->cl(Pool::class)->rq()->remitments());
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
        $this->remitmentService->saveMutualRemitment($this->pool, $this->session, $payableId);
        // $this->notify->success(trans('session.remitment.created'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function deleteRemitment(int $payableId)
    {
        $this->remitmentService->deleteMutualRemitment($this->pool, $this->session, $payableId);
        // $this->notify->success(trans('session.remitment.deleted'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }
}
