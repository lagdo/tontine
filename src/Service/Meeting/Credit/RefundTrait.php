<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

trait RefundTrait
{
    /**
     * @var TenantService
     */
    private TenantService $tenantService;

    /**
     * @var FundService
     */
    private FundService $fundService;

    /**
     * @param Session $session The session
     * @param Fund $fund
     * @param bool|null $onlyPaid
     *
     * @return Builder|Relation
     */
    private function getDebtsQuery(Session $session, Fund $fund,
        ?bool $onlyPaid): Builder|Relation
    {
        $prevSessions = $this->fundService->getFundSessionIds($session, $fund)
            ->filter(fn(int $sessionId) => $sessionId !== $session->id);

        return Debt::whereHas('loan', function(Builder $query) use($fund) {
                $query->where('fund_id', $fund->id);
            })
            ->when($onlyPaid === false, function(Builder $query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function(Builder $query) {
                return $query->whereHas('refund');
            })
            ->where(function(Builder $query) use($session, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function(Builder $query) use($session) {
                    $query->whereHas('loan', function(Builder $query) use($session) {
                        $query->where('session_id', $session->id);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function(Builder $query) use($session, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($prevSessions) {
                        $query->whereIn('session_id', $prevSessions);
                    })
                    ->where(function(Builder $query) use($session) {
                        // The debts that are not yet refunded.
                        $query->orWhereDoesntHave('refund');
                        // The debts that are refunded in the current session.
                        $query->orWhereHas('refund', function(Builder $query) use($session) {
                            $query->where('session_id', $session->id);
                        });
                    });
                });
            });
    }

    /**
     * @param int $debtId
     *
     * @return Debt|null
     */
    public function getDebt(int $debtId): ?Debt
    {
        return Debt::whereHas('loan', function(Builder|Relation $query) {
                $query->whereHas('member', function(Builder|Relation $query) {
                    $query->where('tontine_id', $this->tenantService->tontine()->id);
                });
            })
            ->find($debtId);
    }
}
