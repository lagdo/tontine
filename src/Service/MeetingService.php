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
     * @param TenantService $tenantService
     * @param PlanningService $planningService
     * @param DepositService $depositService
     * @param RemittanceService $remittanceService
     */
    public function __construct(TenantService $tenantService, PlanningService $planningService,
        DepositService $depositService, RemittanceService $remittanceService)
    {
        $this->tenantService = $tenantService;
        $this->planningService = $planningService;
        $this->depositService = $depositService;
        $this->remittanceService = $remittanceService;
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
     * Get a paginated list of funds with receivables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFundsWithReceivables(Session $session, int $page = 0): Collection
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
        });
    }

    /**
     * Get a paginated list of funds with payables.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFundsWithPayables(Session $session, int $page = 0): Collection
    {
        $funds = $this->tenantService->round()->funds();
        if($page > 0 )
        {
            $funds->take($this->tenantService->getLimit());
            $funds->skip($this->tenantService->getLimit() * ($page - 1));
        }

        $sessions = $this->tenantService->round()->sessions;

        return $funds->get()->each(function($fund) use($session, $sessions) {
            // Payables
            $payables = $this->remittanceService->getPayables($fund, $session);
            // Expected
            // $fund->pay_count = $payables->count();
            // Paid
            $fund->pay_paid = $payables->filter(function($payable) {
                return $payable->remittance !== null;
            })->count();

            // Remittances
            $sessions = $sessions->filter(function($_session) use($fund) {
                return $_session->enabled($fund);
            });
            $sessionCount = $sessions->count();
            $sessionRank = $sessions->filter(function($_session) use($session) {
                return $_session->start_at->lt($session->start_at);
            })->count();
            $subscriptionCount = $fund->subscriptions()->count();
            $fund->pay_count = $this->planningService->getRemittanceCount($sessionCount, $subscriptionCount, $sessionRank);
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
     * Get funds summary for a session
     *
     * @param Session $session
     *
     * @return array
     */
    public function getFundsSummary(Session $session): array
    {
        $funds = $this->tenantService->round()->funds->keyBy('id');
        $sessions = $this->tenantService->round()->sessions;

        $payableSum = 0;
        $payableAmounts = $session->payableAmounts()->get()
            ->each(function($payable) use($funds, $sessions, &$payableSum) {
                $fund = $funds[$payable->id];
                $count = $sessions->filter(function($session) use($fund) {
                    return $session->enabled($fund);
                })->count();
                $payableSum += $payable->amount * $count;
                $payable->amount = Currency::format($payable->amount * $count);
            })->pluck('amount', 'id');

        $receivableSum = 0;
        $receivableAmounts = $session->receivableAmounts()->get()
            ->each(function($receivable) use(&$receivableSum) {
                $receivableSum += $receivable->amount;
                $receivable->amount = Currency::format($receivable->amount);
            })->pluck('amount', 'id');

        return [
            'payables' => $payableAmounts,
            'receivables' => $receivableAmounts,
            'sum' => [
                'payables' => Currency::format($payableSum),
                'receivables' => Currency::format($receivableSum),
            ],
        ];
    }
}
