<?php

namespace Siak\Tontine\Service\Report;

use Siak\Tontine\Model\Currency;
use Siak\Tontine\Service\Charge\FeeSummaryService;
use Siak\Tontine\Service\Charge\FineSummaryService;
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
     * @var FeeSummaryService
     */
    private $feeSummaryService;

    /**
     * @var FineSummaryService
     */
    private $fineSummaryService;

    /**
     * @param MeetingService $meetingService
     * @param SubscriptionService $subscriptionService
     * @param FeeSummaryService $feeSummaryService
     * @param FineSummaryService $fineSummaryService
     */
    public function __construct(MeetingService $meetingService, SubscriptionService $subscriptionService,
        FeeSummaryService $feeSummaryService, FineSummaryService $fineSummaryService)
    {
        $this->meetingService = $meetingService;
        $this->subscriptionService = $subscriptionService;
        $this->feeSummaryService = $feeSummaryService;
        $this->fineSummaryService = $fineSummaryService;
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
        $summary = $this->meetingService->getPoolsSummary($session);

        return [
            'tontine' => $tontine,
            'session' => $session,
            'deposits' => [
                'session' => $session,
                'pools' => $this->meetingService->getPoolsWithReceivables($session),
                'summary' => $summary['receivables'],
                'sum' => $summary['sum']['receivables'],
            ],
            'remittances' => [
                'session' => $session,
                'pools' => $this->meetingService->getPoolsWithPayables($session),
                'summary' => $summary['payables'],
                'sum' => $summary['sum']['payables'],
            ],
            'fees' => [
                'session' => $session,
                'fees' => $this->meetingService->getFees($session),
                'settlements' => $this->feeSummaryService->getSettlements($session),
                'bills' => $this->feeSummaryService->getBills($session),
                'zero' => $this->feeSummaryService->getFormattedAmount(0),
            ],
            'fines' => [
                'session' => $session,
                'fines' => $this->meetingService->getFines($session),
                'settlements' => $this->fineSummaryService->getSettlements($sessions),
                'bills' => $this->fineSummaryService->getBills($session),
                'zero' => $this->fineSummaryService->getFormattedAmount(0),
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
