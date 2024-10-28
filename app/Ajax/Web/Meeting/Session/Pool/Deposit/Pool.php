<?php

namespace App\Ajax\Web\Meeting\Session\Pool\Deposit;

use App\Ajax\Web\Meeting\MeetingComponent;
use App\Ajax\Web\Meeting\Session\Pool\PoolTrait;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Pool\DepositService;
use Siak\Tontine\Service\Meeting\Pool\PoolService;

use function Jaxon\jaxon;
use function filter_var;
use function str_replace;
use function trans;
use function trim;

/**
 * @before getPool
 */
class Pool extends MeetingComponent
{
    use PoolTrait;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * The constructor
     *
     * @param BalanceCalculator $balanceCalculator
     * @param PoolService $poolService
     * @param DepositService $depositService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        protected PoolService $poolService, protected DepositService $depositService)
    {}

    /**
     * @inheritDoc
     */
    public function html(): string
    {
        return (string)$this->renderView('pages.meeting.deposit.pool.home', [
            'pool' => $this->cache->get('meeting.pool'),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->cl(PoolPage::class)->page();;
    }

    /**
     * @param int $poolId
     *
     * @return mixed
     */
    public function home(int $poolId)
    {
        $this->bag('meeting')->set('pool.id', $poolId);
        $this->bag('meeting')->set('deposit.page', 1);

        return $this->render();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function addDeposit(int $receivableId)
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');
        $this->depositService->createDeposit($pool, $session, $receivableId);

        return $this->cl(PoolPage::class)->page();
    }

    /**
     * @di $localeService
     * @param int $receivableId
     *
     * @return mixed
     */
    public function editAmount(int $receivableId)
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');
        $receivable = $this->depositService->getReceivable($pool, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            return $this->cl(PoolPage::class)->page();
        }

        $html = $this->renderView('pages.meeting.deposit.libre.edit', [
            'receivableId' => $receivable->id,
            'amount' => !$receivable->deposit ? '' :
                $this->localeService->getMoneyValue($receivable->deposit->amount),
        ]);
        jaxon()->getResponse()->html("receivable-{$receivable->id}", $html);

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
        $amount = str_replace(',', '.', trim($amount));
        if($amount !== '' && filter_var($amount, FILTER_VALIDATE_FLOAT) === false)
        {
            $this->notify->title(trans('common.titles.error'))
                ->error(trans('meeting.errors.amount.invalid', ['amount' => $amount]));
            return $this->response;
        }
        $amount = $amount === '' ? 0 : $this->localeService->convertMoneyToInt((float)$amount);

        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');
        $amount > 0 ?
            $this->depositService->saveDepositAmount($pool, $session, $receivableId, $amount):
            $this->depositService->deleteDeposit($pool, $session, $receivableId);

        return $this->cl(PoolPage::class)->page();
    }

    /**
     * @param int $receivableId
     *
     * @return mixed
     */
    public function delDeposit(int $receivableId)
    {
        $pool = $this->cache->get('meeting.pool');
        $session = $this->cache->get('meeting.session');
        $this->depositService->deleteDeposit($pool, $session, $receivableId);

        return $this->cl(PoolPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function addAllDeposits()
    {
        $pool = $this->cache->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $this->depositService->createAllDeposits($pool, $session);

        return $this->cl(PoolPage::class)->page();
    }

    /**
     * @return mixed
     */
    public function delAllDeposits()
    {
        $pool = $this->cache->get('meeting.pool');
        if(!$pool->deposit_fixed)
        {
            return $this->response;
        }

        $session = $this->cache->get('meeting.session');
        $this->depositService->deleteAllDeposits($pool, $session);

        return $this->cl(PoolPage::class)->page();
    }
}
