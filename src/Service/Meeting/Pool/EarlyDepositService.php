<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;

class EarlyDepositService
{
    use DepositServiceTrait;

    /**
     * @param TenantService $tenantService
     * @param PaymentServiceInterface $paymentService
     */
    public function __construct(protected TenantService $tenantService,
        protected PaymentServiceInterface $paymentService)
    {}

    /**
     * @param Pool $pool
     * @param Session $session
     * @param Session|null $nextSession
     * @param bool|null $filter
     *
     * @return Builder|Relation
     */
    private function getReceivableQuery(Pool $pool, Session $session,
        ?Session $nextSession, ?bool $filter = null): Builder|Relation
    {
        $filterQuery = match($filter) {
            true => fn(Builder $query) => $query->paid(),
            false => fn(Builder $query) => $query->unpaid(),
            default => null,
        };

        return $session->round->receivables()
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id)
            ->early($session)
            ->when($nextSession !== null,
                fn(Builder $query) => $query->whereSession($nextSession))
            ->when($filterQuery !== null, $filterQuery);
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session|null $nextSession
     * @param bool|null $filter
     *
     * @return int
     */
    public function getReceivableCount(Pool $pool, Session $session,
        ?Session $nextSession, ?bool $filter = null): int
    {
        return $this->getReceivableQuery($pool, $session, $nextSession, $filter)->count();
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session|null $nextSession
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getReceivables(Pool $pool, Session $session,
        ?Session $nextSession, ?bool $filter = null, int $page = 0): Collection
    {
        $query = $this->getReceivableQuery($pool, $session, $nextSession, $filter);
        return $this->getReceivableDetailsQuery($query, $page)->get();
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session|null $nextSession
     *
     * @return array
     */
    public function getPoolDepositNumbers(Pool $pool, Session $session, ?Session $nextSession): array
    {
        $deposit = Deposit::where('pool_id', $pool->id)
            ->where('session_id', $session->id)
            ->whereHas('receivable', fn(Builder $qr) => $qr
                ->early($session)->when($nextSession !== null,
                    fn(Builder $query) => $query->whereSession($nextSession)))
            ->select(DB::raw('count(*) as count'),
                DB::raw('sum(amount) as amount'))
            ->first();
        return [$deposit?->amount ?? 0, $deposit?->count ?? 0];
    }

    /**
     * Find the unique receivable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session $nextSession
     * @param int $receivableId
     *
     * @return Receivable|null
     */
    public function getReceivable(Pool $pool, Session $session,
        Session $nextSession, int $receivableId): ?Receivable
    {
        return $this->getReceivableQuery($pool, $session, $nextSession)
            ->with(['deposit'])
            ->find($receivableId);
    }

    /**
     * Create a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session $nextSession
     * @param int $receivableId
     * @param int $amount
     *
     * @return void
     */
    public function createDeposit(Pool $pool, Session $session,
        Session $nextSession, int $receivableId, int $amount = 0): void
    {
        $receivable = $this->getReceivable($pool, $session, $nextSession, $receivableId);
        $this->checkDepositCreation($pool, $receivable, $amount);

        $this->saveDeposit($receivable, $session, $amount);
    }

    /**
     * Delete a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param Session $nextSession
     * @param int $receivableId
     *
     * @return void
     */
    public function deleteDeposit(Pool $pool, Session $session,
        Session $nextSession, int $receivableId): void
    {
        $receivable = $this->getReceivable($pool, $session, $nextSession, $receivableId);
        $this->_deleteDeposit($receivable);
    }
}
