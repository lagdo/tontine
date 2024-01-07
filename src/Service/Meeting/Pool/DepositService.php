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
use Siak\Tontine\Service\TenantService;

use function trans;

class DepositService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return Builder|Relation
     */
    private function getQuery(Pool $pool, Session $session): Builder|Relation
    {
        return $session->receivables()
            ->select('receivables.*')
            ->join('subscriptions', 'subscriptions.id', '=', 'receivables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getReceivables(Pool $pool, Session $session, int $page = 0): Collection
    {
        // The jointure with the subscriptions and members tables is needed,
        // so the final records can be ordered by member name.
        return $this->getQuery($pool, $session)
            ->addSelect(DB::raw('pools.amount, members.name as member'))
            ->join('pools', 'pools.id', '=', 'subscriptions.pool_id')
            ->join('members', 'members.id', '=', 'subscriptions.member_id')
            ->with(['deposit'])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('members.name', 'asc')
            ->orderBy('subscriptions.id', 'asc')
            ->get();
    }

    /**
     * Get the number of receivables in the selected round.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getReceivableCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session)->count();
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
            if((!$receivable->deposit->editable))
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
        if((!$receivable->deposit->editable))
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
        $receivables = $this->getQuery($pool, $session)->whereDoesntHave('deposit')->get();
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
        $receivables = $this->getQuery($pool, $session)
            ->with(['deposit'])
            ->whereHas('deposit')
            ->get()
            ->filter(function($receivable) {
                return $receivable->deposit->editable;
            });
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
        return $this->getQuery($pool, $session)->whereHas('deposit')->count();
    }
}
