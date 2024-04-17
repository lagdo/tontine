<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Saving\SavingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Report\RoundService;
use Siak\Tontine\Service\Tontine\FundService;

use function collect;
use function compact;

class ReportService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param MemberService $memberService
     * @param RoundService $roundService
     * @param FundService $fundService
     * @param SavingService $savingService
     * @param SummaryService $summaryService
     * @param ProfitService $profitService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService,
        protected MemberService $memberService, protected RoundService $roundService,
        protected FundService $fundService, protected SavingService $savingService,
        protected SummaryService $summaryService, protected ProfitService $profitService)
    {}

    /**
     * @param Session $session
     * @param Collection $fundIds
     *
     * @return array
     */
    private function getFundClosings(Session $session, Collection $fundIds): array
    {
        $closings = $this->savingService->getSessionClosings($session);
        return Arr::only($closings, $fundIds->all());
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getClosings(Session $session): array
    {
        $closings = $this->savingService->getSessionClosings($session);
        $funds = $this->fundService->getFundList();
        return Arr::only($closings, $funds->keys()->all());
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSessionReport(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        [$country] = $this->localeService->getNameFromTontine($tontine);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'country' => $country,
            'deposits' => [
                'session' => $session,
                'receivables' => $this->memberService->getReceivables($session),
                'pools' => $this->sessionService->getReceivables($session),
            ],
            'remitments' => [
                'session' => $session,
                'payables' => $this->memberService->getPayables($session),
                'pools' => $this->sessionService->getPayables($session),
                'auctions' => $this->memberService->getAuctions($session),
            ],
            'bills' => [
                'bills' => $this->memberService->getBills($session),
                'charges' => [
                    'session' => $this->sessionService->getSessionCharges($session),
                    'total' => $this->sessionService->getTotalCharges($session),
                ],
            ],
            'loans' => [
                'loans' => $this->memberService->getLoans($session),
                'total' => $this->sessionService->getLoan($session),
            ],
            'refunds' => [
                'refunds' => $this->memberService->getRefunds($session),
                'total' => $this->sessionService->getRefund($session),
            ],
            'savings' => [
                'savings' => $this->memberService->getSavings($session),
                'funds' => $this->fundService->getFundList(),
                'total' => $this->sessionService->getSaving($session),
            ],
            'disbursements' => [
                'disbursements' => $this->memberService->getDisbursements($session),
                'total' => $this->sessionService->getDisbursement($session),
            ],
        ];
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSessionEntry(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        [$country] = $this->localeService->getNameFromTontine($tontine);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'country' => $country,
            'deposits' => [
                'session' => $session,
                'receivables' => $this->memberService->getReceivables($session),
                'pools' => $this->sessionService->getReceivables($session),
            ],
            'remitments' => [
                'session' => $session,
                'payables' => $this->memberService->getPayables($session),
                'pools' => $this->sessionService->getPayables($session),
            ],
            'bills' => [
                'bills' => $this->memberService->getBills($session),
                'charges' => $this->tenantService->tontine()->charges()
                    ->active()->fixed()->get(),
            ],
        ];
    }

    /**
     * @param Session $session
     * @param int $profitAmount
     * @param string $fund
     * @param int $fundId
     *
     * @return array
     */
    private function getFundSavings(Session $session, int $profitAmount,
        string $name, int $fundId): array
    {
        $savings = $this->profitService->getDistributions($session, $fundId, $profitAmount);
        $partUnitValue = $this->profitService->getPartUnitValue($savings);
        $distributionSum = $savings->sum('distribution');
        $distributionCount = $savings->filter(fn($saving) => $saving->distribution > 0)->count();

        return compact('name', 'savings', 'profitAmount', 'partUnitValue',
            'distributionSum', 'distributionCount');
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSavingsReport(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        [$country] = $this->localeService->getNameFromTontine($tontine);
        $funds = $this->fundService->getFundList();
        $closings = $this->getFundClosings($session, $funds->keys());

        $funds = $funds
            ->map(fn($name, $fundId) => $this->getFundSavings($session,
                $closings[$fundId] ?? 0, $name, $fundId))
            ->filter(fn($fund) => $fund['savings']->count() > 0);

        return compact('tontine', 'session', 'country', 'funds');
    }

    /**
     * @param Round $round
     *
     * @return array
     */
    public function getRoundReport(Round $round): array
    {
        $tontine = $this->tenantService->tontine();
        [$country, $currency] = $this->localeService->getNameFromTontine($tontine);

        $pools = $this->summaryService->getFigures($round);
        $sessions = $round->sessions()->orderBy('start_at', 'asc')->get();
        // Sessions with data
        $sessionIds = $sessions->filter(function($session) {
            return $session->status === Session::STATUS_CLOSED ||
                $session->status === Session::STATUS_OPENED;
        })->pluck('id');
        $amounts = [
            'sessions' => $sessions,
            'auctions' => $this->roundService->getAuctionAmounts($sessionIds),
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'disbursements' => $this->roundService->getDisbursementAmounts($sessionIds),
        ];

        return compact('tontine', 'round', 'country', 'currency', 'pools', 'amounts');
    }

    /**
     * @param Round $round
     *
     * @return Collection
     */
    private function getCreditReportFunds(Round $round): Collection
    {
        if(!($session = $round->sessions()->orderByDesc('start_at')->first()))
        {
            return collect();
        }

        return $this->fundService->getActiveFunds()
            ->each(function($fund) use($session) {
                $sessions = $this->profitService->getFundSessions($session, $fund->id);
                $fund->loans = Loan::select('loans.*')
                    ->join('sessions', 'loans.session_id', '=', 'sessions.id')
                    ->whereIn('session_id', $sessions->pluck('id'))
                    ->when($fund->id > 0, fn(Builder $query) =>
                        $query->where('fund_id', $fund->id))
                    ->when($fund->id === 0, fn(Builder $query) =>
                        $query->whereNull('fund_id'))
                    ->with(['member', 'session', 'debts.refund', 'debts.refund.session',
                        'debts.partial_refunds', 'debts.partial_refunds.session'])
                    ->orderBy('sessions.start_at')
                    ->get();
            })
            ->filter(fn($fund) => $fund->loans->count() > 0);
    }

    /**
     * @param Round $round
     *
     * @return array
     */
    public function getCreditReport(Round $round): array
    {
        $tontine = $this->tenantService->tontine();
        [$country, $currency] = $this->localeService->getNameFromTontine($tontine);
        $funds = $this->getCreditReportFunds($round);

        return compact('tontine', 'round', 'country', 'currency', 'funds');
    }
}
