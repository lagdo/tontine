<?php

namespace App\Ajax\App\Meeting\Pool;

use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;
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

    protected function getPool()
    {
        $sessionId = $this->bag('meeting')->get('session.id');

        // No pool id on the "home" page
        if($this->target()->method() === 'home')
        {
            $this->session = $this->poolService->getSession($sessionId);
            return;
        }

        $this->session = $this->depositService->getSession($sessionId);
        $poolId = $this->target()->method() === 'pool' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->depositService->getPool($poolId);
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

        $html = $this->view()->render('tontine.pages.meeting.deposit.home')
            ->with('tontine', $tontine)
            ->with('session', $this->session)
            ->with('pools', $this->poolService->getPoolsWithReceivables($this->session));
        $this->response->html('meeting-deposits', $html);

        $this->jq('#btn-deposits-refresh')->click($this->rq()->home());
        $poolId = jq()->parent()->attr('data-pool-id')->toInt();
        $this->jq('.btn-pool-deposits')->click($this->rq()->pool($poolId));

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

        $html = $this->view()->render('tontine.pages.meeting.deposit.pool', [
            'pool' => $this->pool,
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->jq('#btn-deposits-back')->click($this->rq()->home());

        return $this->page(1);
    }

    /**
     * @param int $pageNumber
     *
     * @return mixed
     */
    public function page(int $pageNumber = 0)
    {
        $receivableCount = $this->depositService->getReceivableCount($this->pool, $this->session);
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $receivableCount, 'meeting', 'deposit.page');
        $receivables = $this->depositService->getReceivables($this->pool, $this->session, $pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $receivableCount);

        $html = $this->view()->render('tontine.pages.meeting.deposit.page', [
            'tontine' => $this->poolService->getTontine(),
            'session' => $this->session,
            'receivables' => $receivables,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-pool-deposits', $html);

        $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-add-deposit')->click($this->rq()->addDeposit($receivableId));
        $this->jq('.btn-del-deposit')->click($this->rq()->delDeposit($receivableId));
        $this->jq('.btn-save-deposit')->click($this->rq()->saveAmount($receivableId, $amount));
        $this->jq('.btn-edit-deposit')->click($this->rq()->editAmount($receivableId));

        return $this->response;
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit(int $receivableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->depositService->createDeposit($this->pool, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.created'), trans('common.titles.success'));

        return $this->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function editAmount(int $receivableId)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $receivable = $this->depositService->getReceivable($this->pool, $this->session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            return $this->page();
        }

        $html = $this->view()->render('tontine.pages.meeting.deposit.libre.edit', [
            'id' => $receivable->id,
            'amount' => !$receivable->deposit ? '' : $receivable->deposit->amount,
        ]);
        $fieldId = 'receivable-' . $receivable->id;
        $this->response->html($fieldId, $html);
        $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val()->toInt();
        $this->jq('.btn-save-deposit', "#$fieldId")->click($this->rq()->saveAmount($receivableId, $amount));

        return $this->response;
    }

    /**
     * @param int $receivableId
     * @param int $amount
     *
     * @return mixed
     */
    public function saveAmount(int $receivableId, int $amount)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $amount > 0 ?
            $this->depositService->saveDepositAmount($this->pool, $this->session, $receivableId, $amount):
            $this->depositService->deleteDeposit($this->pool, $this->session, $receivableId);
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
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }

        $this->depositService->deleteDeposit($this->pool, $this->session, $receivableId);
        // $this->notify->success(trans('session.deposit.deleted'), trans('common.titles.success'));

        return $this->page();
    }
}
