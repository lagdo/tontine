<?php

namespace Siak\Tontine\Service\Meeting\Pool;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Auction;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\SummaryService;

use function trans;

class RemitmentService
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var SummaryService
     */
    protected SummaryService $summaryService;

    /**
     * @var BalanceCalculator
     */
    protected BalanceCalculator $balanceCalculator;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SummaryService $summaryService
     * @param BalanceCalculator $balanceCalculator
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        SummaryService $summaryService, BalanceCalculator $balanceCalculator)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->summaryService = $summaryService;
        $this->balanceCalculator = $balanceCalculator;
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a single pool.
     *
     * @param int $poolId    The pool id
     *
     * @return Pool|null
     */
    public function getPool(int $poolId): ?Pool
    {
        return $this->tenantService->round()->pools()->with(['subscriptions.payable.remitment'])->find($poolId);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Pool $pool, Session $session)
    {
        return $session->payables()->whereIn('subscription_id', $pool->subscriptions()->pluck('id'));
    }

    /**
     * Get the number of payables in the selected round.
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
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPayables(Pool $pool, Session $session, int $page = 0): Collection
    {
        $payables = $this->getQuery($pool, $session)
            ->with(['subscription.member', 'remitment', 'remitment.auction'])
            ->page($page, $this->tenantService->getLimit())
            ->get();
        if(!$pool->remit_fixed)
        {
            // When the remitment amount is not fixed, the user decides the amount.
            return $payables->each(function($payable) {
                $payable->amount = $payable->remitment ? $payable->remitment->amount : 0;
            });
        }

        // Set the amount on the payables
        $payables = $payables->each(function($payable) use($session) {
            $payable->amount = $this->balanceCalculator->getPayableAmount($payable, $session);
        });
        if(!$pool->remit_planned)
        {
            return $payables;
        }

        // When the number of remitments is planned, the list is padded to the expected number.
        $remitmentCount = $this->summaryService->getSessionRemitmentCount($pool, $session);
        $emptyPayable = (object)[
            'id' => 0,
            'amount' => $pool->amount * $this->balanceCalculator->enabledSessionCount($pool),
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
            ->with(['remitment', 'remitment.auction'])->find($payableId);
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
     * @param int $amount
     * @param int $auction
     *
     * @return void
     */
    public function saveRemitment(Pool $pool, Session $session, int $payableId,
        int $amount, int $auction): void
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
        // Not needed, since the query filters on remitment inexistence.
        // if($payable->remitment)
        // {
        //     return;
        // }

        DB::transaction(function() use($pool, $session, $payable, $amount, $auction) {
            // Associate the payable with the session.
            $payable->session()->associate($session);
            $payable->save();
            // Create the remitment.
            $remitment = $payable->remitment()->create(['amount' => $amount]);

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
     *
     * @return Collection
     */
    public function getSubscriptions(Pool $pool): Collection
    {
        // Return the member names, keyed by payable id.
        return $pool->subscriptions()
            ->with(['payable', 'member'])
            ->get()
            ->filter(function($subscription) {
                return !$subscription->payable->session_id;
            })->pluck('member.name', 'payable.id');
    }
}
