<?php

namespace Siak\Tontine\Service\Report;

use Siak\Tontine\Model\Currency;
use Siak\Tontine\Service\Charge\FeeReportService;
use Siak\Tontine\Service\Charge\FineReportService;
use Siak\Tontine\Service\Meeting\MeetingService;
use Siak\Tontine\Service\Planning\SubscriptionService;

class ReportService implements ReportServiceInterface
{
    /**
     * @var MeetingService
     */
    private $meetingService;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var FeeReportService
     */
    private $feeReportService;

    /**
     * @var FineReportService
     */
    private $fineReportService;

    /**
     * @param MeetingService $meetingService
     * @param SubscriptionService $subscriptionService
     * @param FeeReportService $feeReportService
     * @param FineReportService $fineReportService
     */
    public function __construct(MeetingService $meetingService, SubscriptionService $subscriptionService,
        FeeReportService $feeReportService, FineReportService $fineReportService)
    {
        $this->meetingService = $meetingService;
        $this->subscriptionService = $subscriptionService;
        $this->feeReportService = $feeReportService;
        $this->fineReportService = $fineReportService;
    }


    /**
     * @param integer $sessionId
     *
     * @return array
     */
    public function getSession(int $sessionId): array
    {
        $tontine = $this->meetingService->getTontine();
        $session = $this->meetingService->getSession($sessionId);
        $report = $this->meetingService->getPoolsReport($session);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'deposits' => [
                'session' => $session,
                'pools' => $this->meetingService->getPoolsWithReceivables($session),
                'report' => $report['receivables'],
                'sum' => $report['sum']['receivables'],
            ],
            'remittances' => [
                'session' => $session,
                'pools' => $this->meetingService->getPoolsWithPayables($session),
                'report' => $report['payables'],
                'sum' => $report['sum']['payables'],
            ],
            'fees' => [
                'session' => $session,
                'fees' => $this->meetingService->getFees($session),
                'settlements' => $this->feeReportService->getSettlements($session),
                'bills' => $this->feeReportService->getBills($session),
                'zero' => $this->feeReportService->getFormattedAmount(0),
            ],
            'fines' => [
                'session' => $session,
                'fines' => $this->meetingService->getFines($session),
                'settlements' => $this->fineReportService->getSettlements($sessions),
                'bills' => $this->fineReportService->getBills($session),
                'zero' => $this->fineReportService->getFormattedAmount(0),
            ],
        ];

        /*if($tontine->is_financial)
        {
            [$biddings, $sum] = $biddingService->getSessionBiddings($session);
            $amountAvailable = $biddingService->getAmountAvailable($session);
            $html->with('biddings', [
                'session' => $session,
                'biddings' => $biddings,
                'sum' => $sum,
                'amountAvailable' => Currency::format($amountAvailable),
            ]);
            $html->with('refunds', [
                'session' => $session,
                'biddings' => $refundService->getBiddings($session, true),
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

        return [
            'figures' => $this->meetingService->getFigures($pool),
            'tontine' => $this->meetingService->getTontine(),
            'pool' => $pool,
            'pools' > $this->subscriptionService->getPools(),
        ];
    }
}
