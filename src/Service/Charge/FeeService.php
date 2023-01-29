<?php

namespace Siak\Tontine\Service\Charge;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class FeeService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var FeeReportService
     */
    protected FeeReportService $reportService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService, FeeReportService $reportService)
    {
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
     * Get a paginated list of fees.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFees(int $page = 0): Collection
    {
        $fees = $this->tenantService->tontine()->charges()->fee()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $fees->take($this->tenantService->getLimit());
            $fees->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $fees->get();
    }

    /**
     * Get the number of fees.
     *
     * @return int
     */
    public function getFeeCount(): int
    {
        return $this->tenantService->tontine()->charges()->fee()->count();
    }

    /**
     * Get the bills and settlements for a given session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getBills(Session $session): array
    {
        return [
            $this->reportService->getBills($session),
            $this->reportService->getSettlements($session),
        ];
    }
}
