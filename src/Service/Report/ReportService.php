<?php

namespace Siak\Tontine\Service\Report;

use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Charge\FeeService;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Service\Charge\FineService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Service\Meeting\ReportService as MeetingReportService;
use Siak\Tontine\Service\Planning\SubscriptionService;

class ReportService implements ReportServiceInterface
{
    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var FeeService
     */
    private $feeService;

    /**
     * @var FeeReportService
     */
    private $feeReportService;

    /**
     * @var FineService
     */
    private $fineService;

    /**
     * @var FineReportService
     */
    private $fineReportService;

    /**
     * @var MeetingReportService
     */
    private $meetingReportService;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @param LocaleService $localeService
     * @param FeeReportService $feeReportService
     * @param FeeService $feeService
     * @param FineReportService $fineReportService
     * @param FineService $fineService
     * @param MeetingReportService $meetingReportService
     * @param SubscriptionService $subscriptionService
     */
    public function __construct(LocaleService $localeService, FeeReportService $feeReportService,
        FeeService $feeService, FineReportService $fineReportService, FineService $fineService,
        MeetingReportService $meetingReportService, SubscriptionService $subscriptionService)
    {
        $this->localeService = $localeService;
        $this->feeService = $feeService;
        $this->feeReportService = $feeReportService;
        $this->fineService = $fineService;
        $this->fineReportService = $fineReportService;
        $this->meetingReportService = $meetingReportService;
        $this->subscriptionService = $subscriptionService;
    }


    /**
     * @param integer $sessionId
     *
     * @return array
     */
    public function getSession(int $sessionId): array
    {
        $tontine = $this->meetingReportService->getTontine();
        $session = $this->meetingReportService->getSession($sessionId);
        $report = $this->meetingReportService->getPoolsReport($session);
        [$countries] = $this->localeService->getNamesFromTontine($tontine);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'countries' => $countries,
            'deposits' => [
                'session' => $session,
                'pools' => $this->meetingReportService->getPoolsWithReceivables($session),
                'report' => $report['receivables'],
                'sum' => $report['sum']['receivables'],
            ],
            'remitments' => [
                'session' => $session,
                'pools' => $this->meetingReportService->getPoolsWithPayables($session),
                'report' => $report['payables'],
                'sum' => $report['sum']['payables'],
            ],
            'fees' => [
                'session' => $session,
                'fees' => $this->feeService->getFees($session),
                'settlements' => $this->feeReportService->getSettlements($session),
                'bills' => $this->feeReportService->getBills($session),
            ],
            'fines' => [
                'session' => $session,
                'fines' => $this->fineService>getFines($session),
                'settlements' => $this->fineReportService->getSettlements($sessions),
                'bills' => $this->fineReportService->getBills($session),
            ],
        ];

        /*if($tontine->is_financial)
        {
            [$loans, $sum] = $loanService->getSessionLoans($session);
            $amountAvailable = $loanService->getAmountAvailable($session);
            $html->with('loans', [
                'session' => $session,
                'loans' => $loans,
                'sum' => $sum,
                'amountAvailable' => $this->localeService->formatMoney($amountAvailable),
            ]);
            $html->with('refunds', [
                'session' => $session,
                'loans' => $refundService->getLoans($session, true),
                'refundSum' => $refundService->getRefundSum($session),
            ]);
        }*/
    }

    /**
     * @param integer $poolId
     *
     * @return array
     */
    public function getPool(int $poolId): array
    {
        $pool = $this->subscriptionService->getPool($poolId);
        $tontine = $this->meetingReportService->getTontine();
        [$countries, $currencies] = $this->localeService->getNamesFromTontine($tontine);

        return [
            'figures' => $this->meetingReportService->getFigures($pool),
            'tontine' => $tontine,
            'countries' => $countries,
            'currencies' => $currencies,
            'pool' => $pool,
            'pools' > $this->subscriptionService->getPools(),
        ];
    }
}
