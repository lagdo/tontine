<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Refund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

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
     * @param string $type
     * @param int $sessionId
     * @param Collection $prevSessions
     * @param bool $onlyPaid
     *
     * @return Builder
     */
    private function getQuery(string $type, int $sessionId, Collection $prevSessions, ?bool $onlyPaid): Builder
    {
        return Debt::whereType($type)
            ->when($onlyPaid === false, function($query) {
                return $query->whereDoesntHave('refund');
            })
            ->when($onlyPaid === true, function($query) {
                return $query->whereHas('refund');
            })
            ->where(function($query) use($sessionId, $prevSessions) {
                // Take all the debts in the current session
                $query->where(function($query) use($sessionId) {
                    $query->whereHas('loan', function(Builder $query) use($sessionId) {
                        $query->where('session_id', $sessionId);
                    });
                });
                if($prevSessions->count() === 0)
                {
                    return;
                }
                // The debts in the previous sessions.
                $query->orWhere(function($query) use($sessionId, $prevSessions) {
                    $query->whereHas('loan', function(Builder $query) use($prevSessions) {
                        $query->whereIn('session_id', $prevSessions);
                    })
                    ->where(function($query) use($sessionId) {
                        // The debts that are not yet refunded.
                        $query->orWhere(function($query) {
                            $query->whereDoesntHave('refund');
                        });
                        // The debts that are refunded in the current session.
                        $query->orWhere(function($query) use($sessionId) {
                            $query->whereHas('refund', function(Builder $query) use($sessionId) {
                                $query->where('session_id', $sessionId);
                            });
                        });
                    });
                });
            });
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
        return $this->getQuery(Debt::TYPE_PRINCIPAL, $session->id, $prevSessions, $onlyPaid)->count();
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
        return $this->getQuery(Debt::TYPE_INTEREST, $session->id, $prevSessions, $onlyPaid)->count();
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

        return $this->getQuery(Debt::TYPE_PRINCIPAL, $session->id, $prevSessions, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund'])
            ->get();
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

        return $this->getQuery(Debt::TYPE_INTEREST, $session->id, $prevSessions, $onlyPaid)
            ->page($page, $this->tenantService->getLimit())
            ->with(['loan', 'loan.member', 'loan.session', 'refund'])
            ->get();
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
        return $session->refunds()
            ->page($page, $this->tenantService->getLimit())
            ->with('debt.loan.member')
            ->get();
    }

    /**
     * Create a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function createRefund(Session $session, int $debtId): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $debt = Debt::whereHas('loan', function(Builder $query) use($sessionIds) {
            $query->whereIn('session_id', $sessionIds);
        })->find($debtId);
        if(!$debt || $debt->refund)
        {
            return; // Todo: throw an exception
        }

        $refund = new Refund();
        $refund->debt()->associate($debt);
        $refund->session()->associate($session);
        $refund->save();
    }

    /**
     * Delete a refund.
     *
     * @param Session $session The session
     * @param int $debtId
     *
     * @return void
     */
    public function deleteRefund(Session $session, int $debtId): void
    {
        $sessionIds = $this->tenantService->round()->sessions()->pluck('id');
        $debt = Debt::whereHas('loan', function(Builder $query) use($sessionIds) {
            $query->whereIn('session_id', $sessionIds);
        })->find($debtId);
        if(!$debt || !$debt->refund)
        {
            throw new MessageException(trans('tontine.loan.errors.not_found'));
        }
        if(($debt->refund->online))
        {
            throw new MessageException(trans('tontine.loan.errors.online'));
        }
        $debt->refund()->where('session_id', $session->id)->delete();
    }
}
