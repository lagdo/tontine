<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

use function trans;

class DepositService
{
    /**
     * @param TenantService $tenantService
     * @param PaymentServiceInterface $paymentService;
     */
    public function __construct(private TenantService $tenantService,
        private PaymentServiceInterface $paymentService, private SearchSanitizer $searchSanitizer)
    {}

    /**
     * @param Pool $pool
     * @param Session $session
     * @param bool|null $filter
     * @param string $search
     *
     * @return Builder|Relation
     */
    private function getQuery(Pool $pool, Session $session,
        ?bool $filter = null, string $search = ''): Builder|Relation
    {
        $search = $this->searchSanitizer->sanitize($search);

        return $session->receivables()
            ->select('receivables.*')
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filter === true, fn(Builder $query) => $query->whereHas('deposit'))
            ->when($filter === false, fn(Builder $query) => $query->whereDoesntHave('deposit'))
            ->when($search !== '',
                fn(Builder $query) => $query->whereHas('subscription',
                    fn(Builder $qs) => $qs->whereHas('member',
                        fn(Builder $qm) => $qm->search($search))));
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param bool|null $filter
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getReceivables(Pool $pool, Session $session,
        ?bool $filter = null, string $search = '', int $page = 0): Collection
    {
        // The jointure with the subscriptions and members tables is needed,
        // so the final records can be ordered by member name.
        return $this->getQuery($pool, $session, $filter, $search)
            ->addSelect(DB::raw('pd.amount, member_defs.name as member'))
            ->join('pools', 'pools.id', '=', 'subscriptions.pool_id')
            ->join(DB::raw('pool_defs as pd'), 'pools.def_id', '=', 'pd.id')
            ->join('members', 'members.id', '=', 'subscriptions.member_id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->with(['deposit'])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('member_defs.name', 'asc')
            ->orderBy('subscriptions.id', 'asc')
            ->get();
    }

    /**
     * Get the number of receivables in the selected round.
     *
     * @param Pool $pool
     * @param Session $session
     * @param bool|null $filter
     * @param string $search
     *
     * @return int
     */
    public function getReceivableCount(Pool $pool, Session $session,
        ?bool $filter = null, string $search = ''): int
    {
        return $this->getQuery($pool, $session, $filter, $search)->count();
    }

    /**
     * Find the unique receivable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return Receivable|null
     */
    public function getReceivable(Pool $pool, Session $session, int $receivableId): ?Receivable
    {
        return $this->getQuery($pool, $session)
            ->with(['deposit'])
            ->find($receivableId);
    }

    /**
     * Create a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function createDeposit(Pool $pool, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        if(!$receivable || $receivable->deposit || !$pool->deposit_fixed)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }

        $deposit = new Deposit();
        $deposit->receivable()->associate($receivable);
        $deposit->session()->associate($session);
        $deposit->save();
    }

    /**
     * Create a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     * @param int $amount
     *
     * @return void
     */
    public function saveDepositAmount(Pool $pool, Session $session, int $receivableId, int $amount): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        if(!$receivable || $pool->deposit_fixed)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }

        if($receivable->deposit !== null)
        {
            if(!$this->paymentService->isEditable($receivable->deposit))
            {
                throw new MessageException(trans('tontine.errors.editable'));
            }

            $receivable->deposit->amount = $amount;
            $receivable->deposit->save();
            return;
        }

        $deposit = new Deposit();
        $deposit->amount = $amount;
        $deposit->receivable()->associate($receivable);
        $deposit->session()->associate($session);
        $deposit->save();
    }

    /**
     * Delete a deposit.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function deleteDeposit(Pool $pool, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(!$this->paymentService->isEditable($receivable->deposit))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $receivable->deposit()->delete();
    }

    /**
     * Create all deposits for a pool.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return void
     */
    public function createAllDeposits(Pool $pool, Session $session): void
    {
        $receivables = $this->getQuery($pool, $session, false)->get();
        if($receivables->count() === 0)
        {
            return;
        }

        DB::transaction(function() use($session, $receivables) {
            foreach($receivables as $receivable)
            {
                $deposit = new Deposit();
                $deposit->receivable()->associate($receivable);
                $deposit->session()->associate($session);
                $deposit->save();
            }
        });
    }

    /**
     * Delete all deposits for a pool.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return void
     */
    public function deleteAllDeposits(Pool $pool, Session $session): void
    {
        $receivables = $this->getQuery($pool, $session, true)
            ->with(['deposit'])
            ->get()
            ->filter(fn($receivable) => $this->paymentService->isEditable($receivable->deposit));
        if($receivables->count() === 0)
        {
            return;
        }

        Deposit::whereIn('receivable_id', $receivables->pluck('id'))
            ->where('session_id', $session->id)
            ->delete();
    }

    /**
     * Delete all deposits for a pool.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return int
     */
    public function countDeposits(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session, true)->count();
    }
}
