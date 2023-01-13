<?php

namespace App\Ajax\App\Meeting;

use Siak\Tontine\Service\Meeting\DepositService;
use Siak\Tontine\Model\Session as SessionModel;
use Siak\Tontine\Model\Pool as PoolModel;
use App\Ajax\CallableClass;

use function Jaxon\jq;
use function trans;

/**
 * @databag meeting
 * @before getPool
 */
class Deposit extends CallableClass
{
    /**
     * @di
     * @var DepositService
     */
    protected DepositService $depositService;

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
        $sessionId = $this->bag('meeting')->get('session.id');
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->session = $this->depositService->getSession($sessionId);
        $this->pool = $this->depositService->getPool($poolId);
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

        $html = $this->view()->render('pages.meeting.deposit.home', [
            'pool' => $this->pool,
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->jq('#btn-deposits-back')->click($this->cl(Pool::class)->rq()->deposits());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        if($pageNumber < 1)
        {
            $pageNumber = $this->bag('meeting')->get('deposit.page', 1);
        }
        $this->bag('meeting')->set('deposit.page', $pageNumber);

        $receivableCount = $this->depositService->getReceivableCount($this->pool, $this->session);
        $html = $this->view()->render('pages.meeting.deposit.page', [
            'receivables' => $this->depositService->getReceivables($this->pool, $this->session, $pageNumber),
            'pagination' => $this->rq()->page()->paginate($pageNumber, 10, $receivableCount),
        ]);
        $this->response->html('meeting-pool-deposits', $html);

        $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
        $this->jq('.btn-add-deposit')->click($this->rq()->addDeposit($receivableId));
        $this->jq('.btn-del-deposit')->click($this->rq()->delDeposit($receivableId));
        $this->jq('.btn-edit-notes')->click($this->rq()->editNotes($receivableId));

        return $this->response;
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit(int $receivableId)
    {
        $this->depositService->createDeposit($this->pool, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.created'), trans('common.titles.success'));

        return $this->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function delDeposit(int $receivableId)
    {
        $this->depositService->deleteDeposit($this->pool, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
