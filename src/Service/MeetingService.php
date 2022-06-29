<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Collection;

use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;

class MeetingService
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var PlanningService
     */
    protected PlanningService $planningService;

    /**
     * @var DepositService
     */
    protected DepositService $depositService;

    /**
     * @var RemittanceService
     */
    protected RemittanceService $remittanceService;

    /**
     * @var FeeSettlementService
     */
    protected FeeSettlementService $feeService;

    /**
     * @var FineSettlementService
     */
    protected FineSettlementService $fineService;

    /**
     * @param TenantService $tenantService
     * @param PlanningService $planningService
     * @param DepositService $depositService
     * @param RemittanceService $remittanceService
     * @param FeeSettlementService $feeService
     * @param FineSettlementService $fineService
     */
    public function __construct(TenantService $tenantService, PlanningService $planningService,
        DepositService $depositService, RemittanceService $remittanceService,
        FeeSettlementService $feeService, FineSettlementService $fineService)
    {
        $this->tenantService = $tenantService;
        $this->planningService = $planningService;
        $this->depositService = $depositService;
        $this->remittanceService = $remittanceService;
        $this->feeService = $feeService;
        $this->fineService = $fineService;
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
     * Get a paginated list of funds.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFunds(Session $session, int $page = 0): Collection
    {
        $funds = $this->tenantService->round()->funds();
        if($page > 0 )
        {
            $funds->take($this->tenantService->getLimit());
            $funds->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $funds->get()->each(function($fund) use($session) {
            // Receivables
            $receivables = $this->depositService->getReceivables($fund, $session);
            // Expected
            $fund->recv_count = $receivables->count();
            // Paid
            $fund->recv_paid = $receivables->filter(function($receivable) {
                return $receivable->deposit !== null;
            })->count();

            // Payables
            $payables = $this->remittanceService->getPayables($fund, $session);
            // Expected
            $fund->pay_count = $payables->count();
            // Paid
            $fund->pay_paid = $payables->filter(function($payable) {
                return $payable->remittance !== null;
            })->count();
        });
    }

    /**
     * Update a session agenda.
     *
     * @param Session $session
     * @param string $agenda
     *
     * @return void
     */
    public function updateSessionAgenda(Session $session, string $agenda): void
    {
        $session->update(['agenda' => $agenda]);
    }

    /**
     * Update a session report.
     *
     * @param Session $session
     * @param string $report
     *
     * @return void
     */
    public function updateSessionReport(Session $session, string $report): void
    {
        $session->update(['report' => $report]);
    }

    /**
     * Find the unique receivable for a fund and a session.
     *
     * @param Fund $fund The fund
     * @param Session $session The session
     * @param int $receivableId
     * @param string $notes
     *
     * @return int
     */
    public function saveReceivableNotes(Fund $fund, Session $session, int $receivableId, string $notes): int
    {
        return $session->receivables()->where('id', $receivableId)
            ->whereIn('subscription_id', $fund->subscriptions()->pluck('id'))->update(['notes' => $notes]);
    }

    /**
     * Get a paginated list of charges.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(Session $session, int $page = 0): Collection
    {
        $charges = $this->tenantService->tontine()->charges()->orderBy('id', 'desc');
        if($page > 0 )
        {
            $charges->take($this->tenantService->getLimit());
            $charges->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $charges->get()->each(function($charge) use($session) {
            $settlementService = $charge->is_fee ? $this->feeService : $this->fineService;
            $charge->members_count = $settlementService->getMemberCount($charge, $session);
            $charge->members_paid = $settlementService->getMemberCount($charge, $session, true);
        });
    }

    /**
     * Get the number of charges.
     *
     * @return int
     */
    public function getChargeCount(): int
    {
        return $this->tenantService->tontine()->charges()->count();
    }

    /**
     * Get the receivables of a given fund.
     *
     * Will return extended data on subscriptions.
     *
     * @param Fund $fund
     *
     * @return array
     */
    public function getFigures(Fund $fund): array
    {
        return $this->planningService->getFigures($fund);
    }

    /**
     * Get a session summary
     *
     * @param Session $session
     *
     * @return array
     */
    public function getFundsSummary(Session $session): array
    {
        $funds = $this->tenantService->round()->funds->keyBy('id');
        $sessions = $this->tenantService->round()->sessions;
        $payableAmounts = $session->payableAmounts()->get()
            ->each(function($payable) use($funds, $sessions) {
                $fund = $funds[$payable->id];
                $count = $sessions->filter(function($session) use($fund) {
                    return $session->enabled($fund);
                })->count();
                $payable->amount = Currency::format($payable->amount * $count);
            })->pluck('amount', 'id');
        $receivableAmounts = $session->receivableAmounts()->get()
            ->each(function($receivable) {
                $receivable->amount = Currency::format($receivable->amount);
            })->pluck('amount', 'id');

        return [
            'payables' => $payableAmounts,
            'receivables' => $receivableAmounts,
        ];
    }
}
