<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return mixed
     */
    private function _getLoanQuery(Session $session)
    {
        // Get the loans of the previous sessions.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<', $session->start_at)->pluck('id');
        return Loan::whereIn('session_id', $sessionIds);
    }

    /**
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return mixed
     */
    private function getLoanQuery(Session $session, ?bool $refunded)
    {
        if($refunded === false)
        {
            // Loans with no refund.
            return $this->_getLoanQuery($session)->whereDoesntHave('refund');
        }
        $filter = function(Builder $query) use($session) {
            $query->where('session_id', $session->id);
        };
        if($refunded === true)
        {
            // Loans with refund on the current session.
            return $this->_getLoanQuery($session)->whereHas('refund', $filter);
        }
        // The union of the two above queries.
        return $this->_getLoanQuery($session)->whereDoesntHave('refund')
            ->union($this->_getLoanQuery($session)->whereHas('refund', $filter));
    }

    /**
     * Get the number of loans that are not yet refunded.
     *
     * @param Session $session The session
     * @param bool $refunded
     *
     * @return int
     */
    public function getLoanCount(Session $session, ?bool $refunded): int
    {
        return $this->getLoanQuery($session, $refunded)->count();
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
    public function getLoans(Session $session, ?bool $refunded, int $page = 0): Collection
    {
        $loans = $this->getLoanQuery($session, $refunded);
        if($page > 0 )
        {
            $loans->take($this->tenantService->getLimit());
            $loans->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $loans->with(['refund', 'member'])->get();
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
     *
     * @return void
     */
    public function createRefund(Session $session, int $loanId): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $loan = Loan::whereIn('session_id', $sessionIds)->find($loanId);
        $refund = new Refund();
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
