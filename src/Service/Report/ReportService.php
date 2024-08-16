<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Siak\Tontine\Service\Meeting\Saving\ClosingService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Report\RoundService;
use Siak\Tontine\Service\Tontine\FundService;

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
     * @param ClosingService $closingService
     * @param SummaryService $summaryService
     * @param ProfitService $profitService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService,
        protected MemberService $memberService, protected RoundService $roundService,
        protected FundService $fundService, protected ClosingService $closingService,
        protected SummaryService $summaryService, protected ProfitService $profitService,
        protected DebtCalculator $debtCalculator)
    {}

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
     *
     * @return Collection
     */
    private function getSessionProfitAmounts(Session $session): Collection
    {
        $closings = $this->closingService->getSavingsClosings($session);

        return $closings->keyBy('fund_id')->map(fn($closing) => $closing->profit);
    }

    /**
     * @param Session $session
     * @param Fund $fund
     * @param int $profitAmount
     *
     * @return array
     */
    private function getFundSavingsReport(Session $session, Fund $fund, int $profitAmount): array
    {
        $name = $fund->title;
        $savings = $this->profitService->getDistributions($session, $fund, $profitAmount);
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
        $funds = $this->fundService->getActiveFunds();
        $profits = $this->getSessionProfitAmounts($session);

        $funds = $funds
            ->map(function($fund) use($session, $profits) {
                return $this->getFundSavingsReport($session, $fund, $profits[$fund->id] ?? 0);
            })
            ->filter(fn($report) => $report['savings']->count() > 0);

        return compact('tontine', 'session', 'country', 'funds');
    }

    /**
     * @param Loan $loan
     * @param Session $session
     *
     * @return void
     */
    private function setDebtAmount(Loan $loan, Session $session)
    {
        if(($debt = $loan->i_debt) !== null)
        {
            $loan->iDebtAmount = $this->debtCalculator->getDebtAmount($session, $debt);
        }
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getCreditReport(Session $session): array
    {
        $round = $session->round;
        $tontine = $this->tenantService->tontine();
        [$country, $currency] = $this->localeService->getNameFromTontine($tontine);

        $funds = $this->fundService->getActiveFunds()
            ->each(function($fund) use($session) {
                $sessionIds = $this->fundService->getFundSessionIds($session, $fund);
                $fund->loans = Loan::select('loans.*')
                    ->join('sessions', 'loans.session_id', '=', 'sessions.id')
                    ->where(fn($query) => $query->where('fund_id', $fund->id))
                    ->whereIn('loans.session_id', $sessionIds)
                    ->with(['member', 'session', 'debts.refund', 'debts.refund.session',
                        'debts.partial_refunds' => function($query) use($sessionIds) {
                            $query->whereIn('session_id', $sessionIds);
                        },
                        'debts.partial_refunds.session'])
                    ->orderBy('sessions.start_at')
                    ->get()
                    ->each(fn(Loan $loan) => $this->setDebtAmount($loan, $session))
                    ->groupBy('member.id');
            })
            ->filter(fn($fund) => $fund->loans->count() > 0);

        return compact('tontine', 'round', 'session', 'country', 'currency', 'funds');
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
}
