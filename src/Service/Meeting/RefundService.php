<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;

class RefundService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
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
     * @param Collection $sessionIds
     * @param bool $refunded
     *
     * @return mixed
     */
    private function getPrincipalQuery(Collection $sessionIds, ?bool $refunded)
    {
        $query = Loan::select('member_id', 'session_id', 'id', 'amount',
                DB::raw("'" . Refund::TYPE_PRINCIPAL . "' as type"))
            ->where('amount', '>', 0)
            ->whereIn('session_id', $sessionIds)
            ->withCount([
                'member',
                'refund' => function($query) {
                    $query->where('type', Refund::TYPE_PRINCIPAL);
                }
            ]);
        if($refunded === false)
        {
            $query->whereDoesntHave('refund', function($query) {
                $query->where('type', Refund::TYPE_PRINCIPAL);
            });
        }
        elseif($refunded === true)
        {
            $query->whereHas('refund', function($query) {
                $query->where('type', Refund::TYPE_PRINCIPAL);
            });
        }

        return $query;
    }

    /**
     * @param Collection $sessionIds
     * @param bool $refunded
     *
     * @return mixed
     */
    private function getInterestQuery(Collection $sessionIds, ?bool $refunded)
    {
        $query = Loan::select('member_id', 'session_id', 'id',
                DB::raw('interest as amount'),
                DB::raw("'" . Refund::TYPE_INTEREST . "' as type"))
            ->where('loans.interest', '>', 0)
            ->whereIn('session_id', $sessionIds)
            ->withCount([
                'member',
                'refund' => function($query) {
                    $query->where('type', Refund::TYPE_INTEREST);
                }
            ]);
        if($refunded === false)
        {
            $query->whereDoesntHave('refund', function($query) {
                $query->where('type', Refund::TYPE_INTEREST);
            });
        }
        elseif($refunded === true)
        {
            $query->whereHas('refund', function($query) {
                $query->where('type', Refund::TYPE_INTEREST);
            });
        }

        return $query;
    }

    /**
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return mixed
     */
    private function getDebtQuery(Session $session, ?bool $refunded)
    {
        // Get the loans of the previous sessions.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)->pluck('id');

        // For each loan, 2 "debts" will be displayed:
        // one for the principal, another one for the interest.
        $principal = $this->getPrincipalQuery($sessionIds, $refunded);
        $interest = $this->getInterestQuery($sessionIds, $refunded);

        return $interest->unionAll($principal);
    }

    /**
     * Get the number of loans that are not yet refunded.
     *
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return int
     */
    public function getDebtCount(Session $session, ?bool $refunded): int
    {
        return $this->getDebtQuery($session, $refunded)->count();
    }

    /**
     * Get the loans that are not yet refunded.
     *
     * @param Session $session The session
     * @param bool $refunded
     * @param int $page
     *
     * @return Collection
     */
    public function getDebts(Session $session, ?bool $refunded, int $page = 0): Collection
    {
        $query = $this->getDebtQuery($session, $refunded);
        if($page > 0 )
        {
            $query->take($this->tenantService->getLimit());
            $query->skip($this->tenantService->getLimit() * ($page - 1));
        }
        $debts = $query->get();
        $debts->each(function($debt) {
            $debt->amount = Currency::format($debt->amount);
        });
        return $debts;
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
        return Currency::format($session->refunds()
            ->join('loans', 'loans.id', '=', 'refunds.loan_id')
            ->sum('loans.amount'));
    }
}
