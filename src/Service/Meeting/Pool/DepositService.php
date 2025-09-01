<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Deposit;
use Siak\Tontine\Model\DepositReal;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\PaymentServiceInterface;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

class DepositService
{
    use DepositServiceTrait;

    /**
     * @param TenantService $tenantService
     * @param PaymentServiceInterface $paymentService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(protected TenantService $tenantService,
        protected PaymentServiceInterface $paymentService,
        protected SearchSanitizer $searchSanitizer)
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
        $query = $this->getQuery($pool, $session, $filter, $search);
        return $this->getReceivableDetailsQuery($query, $page)->get();
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
     * @param string $search
     *
     * @return void
     */
    public function createAllDeposits(Pool $pool, Session $session, string $search): void
    {
        $receivables = $this->getQuery($pool, $session, false, $search)->get();
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
     * @param string $search
     *
     * @return void
     */
    public function deleteAllDeposits(Pool $pool, Session $session, string $search): void
    {
        $receivables = $this->getQuery($pool, $session, true, $search)
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
     * @param Pool $pool The pool
     * @param Session $session The session
     *
     * @return array
     */
    public function getPoolDepositNumbers(Pool $pool, Session $session): array
    {
        $pool = Pool::where('id', $pool->id)
            ->select([
                'recv_amount' => Deposit::select(DB::raw('sum(amount)'))
                    ->whereColumn('pool_id', 'pools.id')
                    ->whereHas('receivable', fn(Builder $qr) =>
                        $qr->whereSession($session)->paidHere($session)),
            ])
            ->withCount([
                'receivables as recv_count' => fn(Builder $query) =>
                    $query->whereSession($session),
                'receivables as paid_here' => fn(Builder $query) =>
                    $query->whereSession($session)->paidHere($session),
                'receivables as paid_late' => fn(Builder $query) =>
                    $query->whereSession($session)->paidLater($session),
                'receivables as paid_early' => fn(Builder $query) =>
                    $query->whereSession($session)->paidEarlier($session),
            ])
            ->first();
        return !$pool ? [0, 0, 0] : [
            $pool->recv_amount ??= 0, // The amount paid in the session
            $pool->paid_here, // The number of recv paid in the session
            $pool->recv_count - $pool->paid_late - $pool->paid_early, // The total expected count
        ];
    }
}
