<?php

namespace Siak\Tontine\Service\Report;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Loan;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Meeting\Credit\DebtCalculator;
use Siak\Tontine\Service\Meeting\FundService;
use Siak\Tontine\Service\Meeting\Saving\ProfitService;
use Siak\Tontine\Service\Meeting\SummaryService;
use Siak\Tontine\Service\Report\RoundService;

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
     * @param SummaryService $summaryService
     * @param ProfitService $profitService
     * @param DebtCalculator $debtCalculator
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, protected SessionService $sessionService,
        protected MemberService $memberService, protected RoundService $roundService,
        protected FundService $fundService, protected SummaryService $summaryService,
        protected ProfitService $profitService, protected DebtCalculator $debtCalculator)
    {}

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSessionReport(Session $session): array
    {
        $guild = $this->tenantService->guild();
        [$country] = $this->localeService->getNameFromGuild($guild);

        return [
            'guild' => $guild,
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
                'funds' => $this->fundService->getSessionFundList($session),
                'total' => $this->sessionService->getSaving($session),
            ],
            'outflows' => [
                'outflows' => $this->memberService->getOutflows($session),
                'total' => $this->sessionService->getOutflow($session),
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
        $guild = $this->tenantService->guild();
        [$country] = $this->localeService->getNameFromGuild($guild);

        return [
            'guild' => $guild,
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
                'charges' => $this->tenantService->guild()->charges()
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
        return $session->funds->keyBy('id')->map(fn($fund) => $fund->profit);
    }

    /**
     * @param Session $session
     *
     * @return array
     */
    public function getSavingsReport(Session $session): array
    {
        $guild = $this->tenantService->guild();
        [$country] = $this->localeService->getNameFromGuild($guild);
        $funds = $this->fundService->getSessionFunds($session);
        $profits = $this->getSessionProfitAmounts($session);

        $funds = $funds
            ->map(fn($fund) => [
                'fund' => $fund,
                'distribution' => $this->profitService->getDistribution($session,
                    $fund, $profits[$fund->id] ?? 0),
            ])
            ->filter(fn($report) => $report['distribution']->savings->count() > 0);

        return compact('guild', 'session', 'country', 'funds');
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
            $loan->iDebtAmount = $this->debtCalculator->getDebtAmount($debt, $session);
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
        $guild = $this->tenantService->guild();
        [$country, $currency] = $this->localeService->getNameFromGuild($guild);

        $funds = $this->fundService->getSessionFunds($session)
            ->each(function($fund) use($session) {
                $sessionIds = $this->fundService->getFundSessionIds($fund, $session);
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

        return compact('guild', 'round', 'session', 'country', 'currency', 'funds');
    }

    /**
     * @param Round $round
     *
     * @return array
     */
    public function getRoundReport(Round $round): array
    {
        $guild = $this->tenantService->guild();
        [$country, $currency] = $this->localeService->getNameFromGuild($guild);

        $figures = $this->summaryService->getFigures($round);
      
        $sessions = $this->roundService->getRoundSessions($round);
        $sessionIds = $sessions->pluck('id');
        $balance = [
            'sessions' => $sessions,
            'settlements' => $this->roundService->getSettlementAmounts($sessionIds),
            'loans' => $this->roundService->getLoanAmounts($sessionIds),
            'refunds' => $this->roundService->getRefundAmounts($sessionIds),
            'savings' => $this->roundService->getSavingAmounts($sessionIds),
            'outflows' => $this->roundService->getOutflowAmounts($sessionIds),
            'pools' => $this->summaryService->getPoolsBalance($figures),
        ];

        return compact('guild', 'round', 'country', 'currency', 'figures', 'balance');
    }
}
