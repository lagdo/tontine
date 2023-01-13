<?php

namespace App\Ajax\App\Meeting\Mutual;

use App\Ajax\App\Meeting\Pool;
use App\Ajax\CallableClass;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\Meeting\RemittanceService;

use function intval;
use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Remittance extends CallableClass
{
    /**
     * @di
     * @var RemittanceService
     */
    protected RemittanceService $remittanceService;

    /**
     * @var SessionModel|null
     */
    protected ?SessionModel $session;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

    protected function getPool()
    {
        // Get session
        $sessionId = intval($this->bag('meeting')->get('session.id'));
        $this->session = $this->remittanceService->getSession($sessionId);
        // Get pool
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : intval($this->bag('meeting')->get('pool.id'));
        $this->pool = $this->remittanceService->getPool($poolId);
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

        $payables = $this->remittanceService->getPayables($this->pool, $this->session);
        $html = $this->view()->render('pages.meeting.remittance.mutual', [
            'pool' => $this->pool,
            'payables' => $payables,
        ]);
        $this->response->html('meeting-remittances', $html);
        $this->jq('#btn-remittances-back')->click($this->cl(Pool::class)->rq()->remittances());
        $payableId = jq()->parent()->attr('data-payable-id')->toInt();
        $this->jq('.btn-add-remittance')->click($this->rq()->addRemittance($payableId));
        $this->jq('.btn-del-remittance')->click($this->rq()->delRemittance($payableId));

        return $this->response;
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function addRemittance(int $payableId)
    {
        $this->remittanceService->createRemittance($this->pool, $this->session, $payableId);
        // $this->notify->success(trans('session.remittance.created'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }

    /**
     * @param int $payableId
     *
     * @return mixed
     */
    public function delRemittance(int $payableId)
    {
        $this->remittanceService->deleteRemittance($this->pool, $this->session, $payableId);
        // $this->notify->success(trans('session.remittance.deleted'), trans('common.titles.success'));

        return $this->home($this->pool->id);
    }
}
