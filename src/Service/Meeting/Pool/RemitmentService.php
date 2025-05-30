<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\Session\SummaryService;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Planning\PoolService;

use function trans;

class RemitmentService
{
    /**
     * @param BalanceCalculator $balanceCalculator
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param PoolService $poolService
     * @param SummaryService $summaryService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private LocaleService $localeService, private TenantService $tenantService,
        private PoolService $poolService, private SummaryService $summaryService)
    {}

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return Builder|Relation
     */
    private function getQuery(Pool $pool, Session $session): Builder|Relation
    {
        return $session->payables()
            ->join('subscriptions', 'subscriptions.id', '=', 'payables.subscription_id')
            ->where('subscriptions.pool_id', $pool->id);
    }

    /**
     * Get the number of payables in the given pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getPayableCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session)->count();
    }

    /**
     * Get the number of remitments in the given pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getRemitmentCount(Pool $pool, Session $session): int
    {
        return $this->getQuery($pool, $session)->whereHas('remitment')->count();
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getRemitmentAmount(Pool $pool, Session $session): int
    {
        $count = $this->getRemitmentCount($pool, $session);
        if(!$pool->deposit_fixed)
        {
            // Sum the amounts for all deposits
            return $count === 0 ? 0 :
                $this->balanceCalculator->getPoolDepositAmount($pool, $session);
        }

        return $count * $this->balanceCalculator->getPayableAmount($pool, $session);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPayables(Pool $pool, Session $session, int $page = 0): Collection
    {
        $amount = $this->balanceCalculator->getPayableAmount($pool, $session);
        $payables = $this->getQuery($pool, $session)
            ->select('payables.*')
            ->addSelect(DB::raw('member_defs.name as member'))
            ->join('members', 'members.id', '=', 'subscriptions.member_id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->with(['remitment', 'remitment.auction'])
            ->page($page, $this->tenantService->getLimit())
            ->get()
            ->each(function($payable) use($amount) {
                $payable->amount = $amount;
            });
        if(!$pool->remit_planned)
        {
            return $payables;
        }

        // When the number of remitments is planned, the list is padded to the expected number.
        $remitmentCount = $this->summaryService->getSessionRemitmentCount($pool, $session);
        $emptyPayable = (object)[
            'id' => 0,
            'member' => trans('tontine.remitment.labels.not-assigned'),
            'amount' => $amount,
            'remitment' => null,
        ];

        return $payables->pad($remitmentCount, $emptyPayable);
    }

    /**
     * Find the unique payable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return Payable|null
     */
    public function getPayable(Pool $pool, Session $session, int $payableId): ?Payable
    {
        return $this->getQuery($pool, $session)
            ->with(['remitment', 'remitment.auction'])
            ->select('payables.*')
            ->find($payableId);
    }

    /**
     * Create a remitment.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function savePlannedRemitment(Pool $pool, Session $session, int $payableId): void
    {
        if(!$pool->remit_planned || $pool->remit_auction)
        {
            // Only when remitments are planned and without auctions.
            return;
        }
        // The payable is supposed to already have been associated to the session.
        $payable = $this->getPayable($pool, $session, $payableId);
        if(!$payable)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if($payable->remitment)
        {
            return;
        }
        $payable->remitment()->create([]);
    }

    /**
     * Create a remitment.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     * @param int $auction
     *
     * @return void
     */
    public function saveRemitment(Pool $pool, Session $session, int $payableId, int $auction): void
    {
        // Cannot use the getPayable() method here,
        // because there's no session attached to the payable.
        $payable = Payable::with(['subscription.member'])
            ->whereDoesntHave('remitment')
            ->whereIn('subscription_id', $pool->subscriptions()->pluck('id'))
            ->find($payableId);
        if(!$payable)
        {
            throw new MessageException(trans('tontine.subscription.errors.not_found'));
        }
        if($payable->session_id !== null && $payable->session_id !== $session->id)
        {
            // The selected member is already planned on another session.
            throw new MessageException(trans('tontine.remitment.errors.planning'));
        }
        // If the pool remitments are planned, then check the payable count for the session.
        if($pool->remit_planned)
        {
            $plannedCount = $this->summaryService->getSessionRemitmentCount($pool, $session);
            $remittedCount = $this->getRemitmentCount($pool, $session);
            if($remittedCount >= $plannedCount)
            {
                throw new MessageException(trans('tontine.remitment.errors.max-count'));
            }
        }

        DB::transaction(function() use($pool, $session, $payable, $auction) {
            // Associate the payable with the session.
            $payable->session()->associate($session);
            $payable->save();
            // Create the remitment.
            $remitment = $payable->remitment()->create([]);

            if($pool->remit_auction && $auction > 0)
            {
                // Create the corresponding auction.
                Auction::create([
                    'amount' => $auction,
                    'paid' => true, // The auction is supposed to have been immediatly paid.
                    'session_id' => $session->id,
                    'remitment_id' => $remitment->id,
                ]);
            }
        });
    }

    /**
     * Delete a remitment.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function deleteRemitment(Pool $pool, Session $session, int $payableId): void
    {
        $payable = $this->getQuery($pool, $session)
            ->with(['remitment', 'remitment.auction'])
            ->select('payables.*')
            ->find($payableId);
        if(!$payable || !($remitment = $payable->remitment))
        {
            return;
        }

        DB::transaction(function() use($pool, $payable, $remitment) {
            // Delete the corresponding auction, in case there was one on the remitment.
            $remitment->auction()->delete();
            $remitment->delete();
            // Detach from the session, but only if the remitment was not planned.
            if(!$pool->remit_planned || $pool->remit_auction)
            {
                $payable->session()->dissociate();
                $payable->save();
            }
        });
    }

    /**
     * Get the unpaid subscriptions of a given pool.
     *
     * @param Pool $pool
     * @param Session $session The session
     *
     * @return Collection
     */
    public function getSubscriptions(Pool $pool, Session $session): Collection
    {
        $subscriptions = $pool->subscriptions()
            ->join('members', 'subscriptions.member_id', '=', 'members.id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->orderBy('member_defs.name', 'asc')
            ->orderBy('subscriptions.id', 'asc')
            ->with(['payable', 'member'])
            ->whereHas('payable', fn($query) => $query->whereDoesntHave('remitment'))
            ->select('subscriptions.*')
            ->get();
        if($pool->remit_planned && !$pool->remit_auction)
        {
            // Only the beneficiaries that are not yet planned.
            return $subscriptions
                ->filter(fn($subscription) => !$subscription->payable->session_id)
                ->pluck('member.name', 'payable.id');
        }
        // Also return the beneficiaries that have not yet been remitted.
        return $subscriptions
            ->filter(fn($subscription) => !$subscription->payable->session_id ||
                $subscription->payable->session_id === $session->id)
            ->pluck('member.name', 'payable.id');
    }
}
