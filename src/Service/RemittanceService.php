<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Session;

use Illuminate\Support\Collection;

class RemittanceService
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
        return $this->tenantService->round()->funds()->with(['subscriptions.payable.remittance'])->find($fundId);
    }

    /**
     * @param Fund $fund
     * @param Session $session
     *
     * @return mixed
     */
    private function getQuery(Fund $fund, Session $session)
    {
        return $session->payables()->whereIn('subscription_id', $fund->subscriptions()->pluck('id'));
    }

    /**
     * @param Fund $fund
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPayables(Fund $fund, Session $session, int $page = 0): Collection
    {
        $payables = $this->getQuery($fund, $session)->with(['subscription.member', 'remittance']);
        if($page > 0 )
        {
            $payables->take($this->tenantService->getLimit());
            $payables->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $payables->get();
    }

    /**
     * Get the number of payables in the selected round.
     *
     * @param Fund $fund
     * @param Session $session
     *
     * @return int
     */
    public function getPayableCount(Fund $fund, Session $session): int
    {
        return $this->getQuery($fund, $session)->count();
    }

    /**
     * Find the unique payable for a fund and a session.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $payableId
     *
     * @return Payable|null
     */
    public function getPayable(Fund $fund, Session $session, int $payableId): ?Payable
    {
        return $this->getQuery($fund, $session)->where('id', $payableId)->first();
    }

    /**
     * Create a remittance.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $payableId
     * @param int $amountPaid
     *
     * @return void
     */
    public function createRemittance(Fund $fund, Session $session, int $payableId, int $amountPaid = 0): void
    {
        $payable = $this->getPayable($fund, $session, $payableId);
        if(!$payable || $payable->remittance)
        {
            return;
        }
        $payable->remittance()->create(['paid_at' => now(), 'amount_paid' => $amountPaid]);
    }

    /**
     * Delete a remittance.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $payableId
     *
     * @return void
     */
    public function deleteRemittance(Fund $fund, Session $session, int $payableId): void
    {
        $payable = $this->getPayable($fund, $session, $payableId);
        if(!$payable || !$payable->remittance)
        {
            return;
        }
        $payable->remittance()->delete();
    }
}
