<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class RefundService
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
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
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
     * @param callable $query           A callable that returns the base query
     * @param string $refundRelation
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $onlyPaid
     *
     * @return Builder
     */
    private function getQuery(callable $query, string $refundRelation, int $sessionId,
        Collection $prevSessions, ?bool $onlyPaid): Builder
    {
        // The loans of the current session.
        $sessionQuery = $query()->where('session_id', $sessionId);
        // The filter applies only to this query.
        if($onlyPaid === false)
        {
            $sessionQuery->whereDoesntHave($refundRelation);
        }
        elseif($onlyPaid === true)
        {
            $sessionQuery->whereHas($refundRelation);
        }
        if($prevSessions->count() === 0)
        {
            return $sessionQuery;
        }

        // The loans of the previous sessions that are not yet settled.
        $unpaidQuery = $query()->whereIn('session_id', $prevSessions)->whereDoesntHave($refundRelation);
        // The loans of the previous sessions that are settled in the session.
        $paidQuery = $query()->whereHas($refundRelation, function(Builder $query) use($sessionId) {
            $query->where('session_id', $sessionId);
        });

        if($onlyPaid === false)
        {
            return $sessionQuery->union($unpaidQuery);
        }
        if($onlyPaid === true)
        {
            return $sessionQuery->union($paidQuery);
        }
        return $sessionQuery->union($unpaidQuery)->union($paidQuery);
    }

    /**
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $onlyPaid
     *
     * @return Builder
     */
    private function getPrincipalQuery(int $sessionId, Collection $prevSessions, ?bool $onlyPaid): Builder
    {
        $query = function() {
            return Loan::select('id', 'amount', DB::raw("'principal' as type"),
                'session_id', 'member_id')->where('amount', '>', 0);
        };
        return $this->getQuery($query, 'principal_refund', $sessionId, $prevSessions, $onlyPaid);
    }

    /**
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $onlyPaid
     *
     * @return mixed
     */
    private function getInterestQuery(int $sessionId, Collection $prevSessions, ?bool $onlyPaid)
    {
        $query = function() {
            return Loan::select('id', DB::raw('interest as amount'), DB::raw("'interest' as type"),
                'session_id', 'member_id')->where('interest', '>', 0);
        };
        return $this->getQuery($query, 'interest_refund', $sessionId, $prevSessions, $onlyPaid);
    }

    /**
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return mixed
     */
    private function getDebtQuery(Session $session, ?bool $onlyPaid)
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        // For each loan, 2 "debts" will be displayed:
        // one for the principal, another one for the interest.
        $principal = $this->getPrincipalQuery($session->id, $prevSessions, $onlyPaid);
        $interest = $this->getInterestQuery($session->id, $prevSessions, $onlyPaid);

        return $principal->union($interest);
    }

    /**
     * Get the number of debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getPrincipalDebtCount(Session $session, ?bool $onlyPaid): int
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        return $this->getPrincipalQuery($session->id, $prevSessions, $onlyPaid)->count();
    }

    /**
     * Get the number of debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     *
     * @return int
     */
    public function getInterestDebtCount(Session $session, ?bool $onlyPaid): int
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        return $this->getInterestQuery($session->id, $prevSessions, $onlyPaid)->count();
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getPrincipalDebts(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        $query = $this->getPrincipalQuery($session->id, $prevSessions, $onlyPaid);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $debts = $query->with(['principal_refund', 'member', 'session'])
            ->orderBy('member_id', 'desc')->orderBy('type', 'desc')->get();

        return $debts->each(function($debt) {
            $debt->amount = $this->localeService->formatMoney($debt->amount);
            $debt->refund_id = $debt->principal_refund ? $debt->principal_refund->id : 0;
        });
    }

    /**
     * Get the debts.
     *
     * @param Session $session The session
     * @param bool $onlyPaid
     * @param int $page
     *
     * @return Collection
     */
    public function getInterestDebts(Session $session, ?bool $onlyPaid, int $page = 0): Collection
    {
        $prevSessions = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        $query = $this->getInterestQuery($session->id, $prevSessions, $onlyPaid);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $debts = $query->with(['interest_refund', 'member', 'session'])
            ->orderBy('member_id', 'desc')->orderBy('type', 'desc')->get();

        return $debts->each(function($debt) {
            $debt->amount = $this->localeService->formatMoney($debt->amount);
            $debt->refund_id = $debt->interest_refund ? $debt->interest_refund->id : 0;
        });
    }

    /**
     * Get the refunds for a given session.
     *
     * @param Session $session The session
     * @param int $page
     *
     * @return Collection
     */
    public function getRefunds(Session $session, int $page = 0): Collection
    {
        $refunds = $session->refunds();
        if($page > 0 )
        {
            $refunds->take($this->tenantService->getLimit());
            $refunds->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $refunds->with('loan.member')->get();
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $loanId
     * @param string $type
     *
     * @return void
     */
    public function createRefund(Session $session, int $loanId, string $type): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $loan = Loan::whereIn('session_id', $sessionIds)->find($loanId);

        $refund = new Refund();
        $refund->type = $type;
        $refund->loan()->associate($loan);
        $refund->session()->associate($session);
        $refund->save();
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $refundId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $refundId): void
    {
        $session->refunds()->where('id', $refundId)->delete();
    }

    /**
     * Get the refund sum.
     *
     * @param Session $session The session
     *
     * @return string
     */
    public function getRefundSum(Session $session): string
    {
        return $this->localeService->formatMoney($session->refunds()
            ->join('loans', 'loans.id', '=', 'refunds.loan_id')
            ->sum('loans.amount'));
    }
}
