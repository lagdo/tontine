<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;

class PoolService
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
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param ReportService $reportService
     * @param SessionService $sessionService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        ReportService $reportService, SessionService $sessionService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
        $this->sessionService = $sessionService;
    }

    /**
     * @return Tontine|null
     */
    public function getTontine(): ?Tontine
    {
        return $this->tenantService->tontine();
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
     * Get a paginated list of pools with receivables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithReceivables(Session $session, int $page = 0): Collection
    {
        $pools = $this->tenantService->round()->pools();
        if($page > 0 )
        {
            $pools->take($this->tenantService->getLimit());
            $pools->skip($this->tenantService->getLimit() * ($page - 1));
        }

        // Receivables
        return $pools->withCount([
            'subscriptions as recv_count',
            'subscriptions as recv_paid' => function(Builder $query) use($session) {
                $query->whereHas('receivables', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('deposit');
                });
            },
        ])->get();
    }

    /**
     * Get a paginated list of pools with payables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getPoolsWithPayables(Session $session, int $page = 0): Collection
    {
        $pools = $this->tenantService->round()->pools();
        if($page > 0 )
        {
            $pools->take($this->tenantService->getLimit());
            $pools->skip($this->tenantService->getLimit() * ($page - 1));
        }

        return $pools->withCount([
            'subscriptions as pay_paid' => function(Builder $query) use($session) {
                $query->whereHas('payable', function(Builder $query) use($session) {
                    $query->where('session_id', $session->id)->whereHas('remitment');
                });
            },
        ])->get()->each(function($pool) use($session) {
            // Expected
            $pool->pay_count = $this->reportService->getSessionRemitmentCount($pool, $session);
        });
    }

    /**
     * Get receivable summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getReceivablesSummary(Session $session): array
    {
        $receivableTotal = 0;
        $receivableAmounts = $session->receivableAmounts()->get()
            ->each(function($receivable) use(&$receivableTotal) {
                $receivableTotal += $receivable->amount;
                $receivable->amount = $this->localeService->formatMoney($receivable->amount);
            })->pluck('amount', 'id');

        return [
            'receivables' => $receivableAmounts,
            'total' => $this->localeService->formatMoney($receivableTotal),
        ];
    }

    /**
     * Get payable summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getPayablesSummary(Session $session): array
    {
        $pools = $this->tenantService->round()->pools->keyBy('id');

        $payableTotal = 0;
        $payableAmounts = $session->payableAmounts()->get()
            ->each(function($payable) use($pools, &$payableTotal) {
                $payableAmount = $payable->amount * $this->sessionService->enabledSessionCount($pools[$payable->id]);
                $payableTotal += $payableAmount;
                $payable->amount = $this->localeService->formatMoney($payableAmount);
            })->pluck('amount', 'id');

        return [
            'payables' => $payableAmounts,
            'total' => $this->localeService->formatMoney($payableTotal),
        ];
    }
}
