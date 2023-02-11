<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

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
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param ReportService $reportService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService, ReportService $reportService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
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
     * @param Pool $pool
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPayables(Pool $pool, Session $session, int $page = 0): Collection
    {
        // The remitment amount
        $sessionCount = $this->tenantService->round()->sessions
            ->filter(function($session) use($pool) {
                return $session->enabled($pool);
            })->count();
        $remitmentAmount = $this->localeService->formatMoney($pool->amount * $sessionCount);

        $query = $this->getQuery($pool, $session)->with(['subscription.member', 'remitment']);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $payables = $query->get()->each(function($payable) use($remitmentAmount) {
            $payable->amount = $remitmentAmount;
        });

        $remitmentCount = $this->reportService->getSessionRemitmentCount($pool, $session);
        $emptyPayable = (object)[
            'id' => 0,
            'amount' => $remitmentAmount,
            'remitment' => null,
        ];

        return $payables->pad($remitmentCount, $emptyPayable);
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
        return $this->getQuery($pool, $session)->with(['remitment'])->find($payableId);
    }

    /**
     * Save a remitment for a mutual tontine.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function saveMutualRemitment(Pool $pool, Session $session, int $payableId): void
    {
        $payable = $this->getPayable($pool, $session, $payableId);
        if(!$payable || $payable->remitment)
        {
            return;
        }
        $remitment = $payable->remitment()->create([]);
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
    public function deleteMutualRemitment(Pool $pool, Session $session, int $payableId): void
    {
        $payable = $this->getPayable($pool, $session, $payableId);
        if(($payable) && ($payable->remitment))
        {
            $payable->remitment->delete();
        }
    }

    /**
     * Create a remitment.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $payableId
     * @param int $interest
     *
     * @return void
     */
    public function saveFinancialRemitment(Pool $pool, Session $session, int $payableId, int $interest = 0): void
    {
        // Cannot use the getPayable() method here, because there's no session attached to the payable.
        $payable = Payable::with(['subscription'])
            ->whereDoesntHave('remitment')
            ->whereIn('subscription_id', $pool->subscriptions()->pluck('id'))
            ->find($payableId);
        if(!$payable)
        {
            return;
        }

        DB::transaction(function() use($session, $payable, $interest) {
            // Associate the payable with the session.
            $payable->session()->associate($session);
            $payable->save();
            // Create the remitment.
            $remitment = $payable->remitment()->create([]);
            // Create the corresponding loan.
            // $loan = new Loan();
            $loan = $remitment->loan()->create([
                'amount' => 0,
                'interest' => $interest,
                'member_id' => $payable->subscription->member_id,
                'session_id' => $session->id,
            ]);
            // The loan interest is supposed to have been immediatly refunded.
            $refund = new Refund();
            $refund->type = Refund::TYPE_INTEREST;
            $refund->loan()->associate($loan);
            $refund->session()->associate($session);
            $refund->save();
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
    public function deleteFinancialRemitment(Pool $pool, Session $session, int $payableId): void
    {
        $payable = $this->getQuery($pool, $session)
            ->with(['remitment', 'remitment.loan'])
            ->find($payableId);
        if(($payable) && ($remitment = $payable->remitment))
        {
            DB::transaction(function() use($payable, $remitment) {
                if(($loan = $remitment->loan) != null)
                {
                    $loan->refunds()->delete();
                    $loan->delete();
                }
                $remitment->delete();
                // Detach from the session
                $payable->session()->dissociate();
                $payable->save();
            });
        }
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
        return $pool->subscriptions()->with(['payable', 'member'])->get()
            ->filter(function($subscription) {
                return !$subscription->payable->session_id;
            })->pluck('member.name', 'payable.id');
    }
}
