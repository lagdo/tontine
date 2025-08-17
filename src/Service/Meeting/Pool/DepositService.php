<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\DepositReal;
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
     * @param PaymentServiceInterface $paymentService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(private TenantService $tenantService,
        private PaymentServiceInterface $paymentService,
        private SearchSanitizer $searchSanitizer)
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
        $filterQuery = match($filter) {
            true => fn(Builder $query) => $query->paid(),
            false => fn(Builder $query) => $query->unpaid(),
            default => null,
        };
        $search = $this->searchSanitizer->sanitize($search);

        return $session->receivables()
            ->select('receivables.*')
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id)
            ->when($filterQuery !== null, $filterQuery)
            ->when($search !== '', fn(Builder $query) => $query->search($search));
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
     * @param Receivable $receivable
     * @param Session $session The session
     * @param int $amount
     *
     * @return void
     */
    private function saveDeposit(Receivable $receivable, Session $session, int $amount = 0): void
    {
        if($receivable->deposit !== null)
        {
            // The deposit exists. It is then modified.
            DepositReal::where(['id' => $receivable->deposit->id])
                ->update(['amount' => $amount]);
            return;
        }

        $deposit = new DepositReal();
        $deposit->amount = $amount;
        $deposit->receivable()->associate($receivable);
        $deposit->session()->associate($session);
        $deposit->save();
    }

    /**
     * @param Pool $pool The pool
     * @param Receivable|null $receivable
     * @param int $amount
     *
     * @return void
     */
    private function checkDepositCreation(Pool $pool, ?Receivable $receivable, int $amount = 0): void
    {
        if(!$receivable)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if($pool->deposit_fixed && $receivable->deposit !== null)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(!$pool->deposit_fixed)
        {
            if($amount <= 0)
            {
                throw new MessageException(trans('tontine.subscription.errors.amount'));
            }
            if($receivable->deposit !== null &&
                !$this->paymentService->isEditable($receivable->deposit))
            {
                throw new MessageException(trans('tontine.errors.editable'));
            }
        }
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
    public function createDeposit(Pool $pool, Session $session,
        int $receivableId, int $amount = 0): void
    {
        $receivable = $this->getReceivable($pool, $session, $receivableId);
        $this->checkDepositCreation($pool, $receivable, $amount);

        $this->saveDeposit($receivable, $session, $amount);
    }

    /**
     * Delete a deposit.
     *
     * @param Receivable|null $receivable
     *
     * @return void
     */
    private function _deleteDeposit(?Receivable $receivable): void
    {
        if(!$receivable || !$receivable->deposit)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if(!$this->paymentService->isEditable($receivable->deposit))
        {
            throw new MessageException(trans('tontine.errors.editable'));
        }
        $receivable->deposit_real()->delete();
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
        $this->_deleteDeposit($receivable);
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
                $deposit = new DepositReal();
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
            ->filter(fn($receivable) =>
                $this->paymentService->isEditable($receivable->deposit));
        if($receivables->count() === 0)
        {
            return;
        }

        DepositReal::whereIn('receivable_id', $receivables->pluck('id'))
            ->where('session_id', $session->id)
            ->delete();
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return int
     */
    public function getPoolDepositCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session, true)->count();
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param bool|null $filter
     * @param string $search
     *
     * @return Builder|Relation
     */
    private function getLateReceivableQuery(Pool $pool, Session $session,
        ?bool $filter = null): Builder|Relation
    {
        $filterQuery = match($filter) {
            true => fn(Builder $query) => $query->paid(),
            false => fn(Builder $query) => $query->unpaid(),
            default => null,
        };

        return $session->round->receivables()
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id)
            ->late($session)
            ->when($filterQuery !== null, $filterQuery);
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param bool|null $filter
     *
     * @return int
     */
    public function getLateReceivableCount(Pool $pool, Session $session,
        ?bool $filter = null): int
    {
        return $this->getLateReceivableQuery($pool, $session, $filter)->count();
    }

    /**
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getLateReceivables(Pool $pool, Session $session,
        ?bool $filter = null, int $page = 0): Collection
    {
        return $this->getLateReceivableQuery($pool, $session, $filter)
            ->select('receivables.*', DB::raw('pd.amount, member_defs.name as member'))
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
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return int
     */
    public function getPoolLateDepositCount(Pool $pool, Session $session): int
    {
        return $this->getLateReceivableQuery($pool, $session, true)->count();
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
    public function getLateReceivable(Pool $pool, Session $session, int $receivableId): ?Receivable
    {
        return $this->getLateReceivableQuery($pool, $session)
            ->with(['deposit'])
            ->find($receivableId);
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
    public function createLateDeposit(Pool $pool, Session $session,
        int $receivableId, int $amount = 0): void
    {
        $receivable = $this->getLateReceivable($pool, $session, $receivableId);
        $this->checkDepositCreation($pool, $receivable, $amount);

        $this->saveDeposit($receivable, $session, $amount);
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
    public function deleteLateDeposit(Pool $pool, Session $session, int $receivableId): void
    {
        $receivable = $this->getLateReceivable($pool, $session, $receivableId);
        $this->_deleteDeposit($receivable);
    }
}
