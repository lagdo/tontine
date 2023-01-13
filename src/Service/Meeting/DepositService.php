<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Receivable;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;

class DepositService
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
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     *
     * @return Fund|null
     */
    public function getFund(int $fundId): ?Fund
    {
        return $this->tenantService->round()->funds()->find($fundId);
    }

    /**
     * @param Fund $fund
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Fund $fund, Session $session)
    {
        return $session->receivables()->whereIn('subscription_id', $fund->subscriptions()->pluck('id'));
    }

    /**
     * @param Fund $fund
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getReceivables(Fund $fund, Session $session, int $page = 0): Collection
    {
        $receivables = $this->getQuery($fund, $session)->with(['subscription.member', 'deposit']);
        if($page > 0 )
        {
            $receivables->take($this->tenantService->getLimit());
            $receivables->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $receivables->get();
    }

    /**
     * Get the number of receivables in the selected round.
     *
     * @param Fund $fund
     * @param Session $session
     *
     * @return int
     */
    public function getReceivableCount(Fund $fund, Session $session): int
    {
        return $this->getQuery($fund, $session)->count();
    }

    /**
     * Find the unique receivable for a fund and a session.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return Receivable|null
     */
    public function getReceivable(Fund $fund, Session $session, int $receivableId): ?Receivable
    {
        return $this->getQuery($fund, $session)->where('id', $receivableId)->first();
    }

    /**
     * Create a deposit.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function createDeposit(Fund $fund, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($fund, $session, $receivableId);
        if(!$receivable || $receivable->deposit)
        {
            return;
        }
        $receivable->deposit()->create(['paid_at' => now()]);
    }

    /**
     * Delete a deposit.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $receivableId
     *
     * @return void
     */
    public function deleteDeposit(Fund $fund, Session $session, int $receivableId): void
    {
        $receivable = $this->getReceivable($fund, $session, $receivableId);
        if(!$receivable || !$receivable->deposit)
        {
            return;
        }
        $receivable->deposit()->delete();
    }
}
