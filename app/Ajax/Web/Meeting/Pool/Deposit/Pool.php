<?php

namespace App\Ajax\Web\Meeting\Pool\Deposit;

use App\Ajax\CallableSessionClass;
use App\Ajax\Web\Meeting\Pool\Deposit;
use Siak\Tontine\Model\Pool as PoolModel;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jq;
use function filter_var;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getPool
 */
class Pool extends CallableSessionClass
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var PoolModel|null
     */
    protected ?PoolModel $pool;

    /**
     * The constructor
     *
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(protected PoolService $poolService,
        protected DepositService $depositService)
    {}

    protected function getPool()
    {
        $poolId = $this->target()->method() === 'home' ?
            $this->target()->args()[0] : $this->bag('meeting')->get('pool.id');
        $this->pool = $this->poolService->getPool($poolId);

        if(!$this->session || !$this->pool || $this->session->disabled($this->pool))
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

        $html = $this->render('pages.meeting.deposit.pool.home', [
            'pool' => $this->pool,
        ]);
        $this->response->html('meeting-deposits', $html);
        $this->jq('#btn-deposits-back')->click($this->cl(Deposit::class)->rq()->home());

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

        $html = $this->render('pages.meeting.deposit.pool.page', [
            'pool' => $this->pool,
            'session' => $this->session,
            'receivableCount' => $receivableCount,
            'depositCount' => $this->depositService->countDeposits($this->pool, $this->session),
            'receivables' => $receivables,
            'pagination' => $pagination,
        ]);
        $this->response->html('meeting-pool-deposits', $html);

        $this->jq('.btn-add-all-deposits')->click($this->rq()->addAllDeposits());
        $this->jq('.btn-del-all-deposits')->click($this->rq()->delAllDeposits());
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

        return $this->page();
    }

    /**
     * @di $localeService
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

        $html = $this->render('pages.meeting.deposit.libre.edit', [
            'id' => $receivable->id,
            'amount' => !$receivable->deposit ? '' :
                $this->localeService->getMoneyValue($receivable->deposit->amount),
        ]);
        $fieldId = 'receivable-' . $receivable->id;
        $this->response->html($fieldId, $html);

        $receivableId = jq()->parent()->attr('data-receivable-id')->toInt();
        $amount = jq('input', jq()->parent()->parent())->val();
        $this->jq('.btn-save-deposit', "#$fieldId")->click($this->rq()->saveAmount($receivableId, $amount));

        return $this->response;
    }

    /**
     * @di $localeService
     * @param int $receivableId
     * @param string $amount
     *
     * @return mixed
     */
    public function saveAmount(int $receivableId, string $amount)
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            $this->notify->error(trans('meeting.errors.amount.invalid', ['amount' => $amount]));
            return $this->response;
        }
        $amount = $amount === '' ? 0 : $this->localeService->convertMoneyToInt((float)$amount);

        $amount > 0 ?
            $this->depositService->saveDepositAmount($this->pool, $this->session, $receivableId, $amount):
            $this->depositService->deleteDeposit($this->pool, $this->session, $receivableId);

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

        return $this->page();
    }

    /**
     * @return mixed
     */
    public function addAllDeposits()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if(!$this->pool->deposit_fixed)
        {
            return $this->response;
        }

        $this->depositService->createAllDeposits($this->pool, $this->session);

        return $this->page();
    }

    /**
     * @return mixed
     */
    public function delAllDeposits()
    {
        if($this->session->closed)
        {
            $this->notify->warning(trans('meeting.warnings.session.closed'));
            return $this->response;
        }
        if(!$this->pool->deposit_fixed)
        {
            return $this->response;
        }

        $this->depositService->deleteAllDeposits($this->pool, $this->session);

        return $this->page();
    }
}
