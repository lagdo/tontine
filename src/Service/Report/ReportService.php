<?php

namespace Siak\Tontine\Service\Report;

use Siak\Tontine\Model\Currency;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\SubscriptionService;

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
     * @var FeeSettlementService
     */
    private $feeSettlementService;

    /**
     * @var FineSettlementService
     */
    private $fineSettlementService;

    /**
     * @param MeetingService $meetingService
     * @param SubscriptionService $subscriptionService
     * @param FeeSettlementService $feeSettlementService
     * @param FineSettlementService $fineSettlementService
     */
    public function __construct(MeetingService $meetingService, SubscriptionService $subscriptionService,
        FeeSettlementService $feeSettlementService, FineSettlementService $fineSettlementService)
    {
        $this->meetingService = $meetingService;
        $this->subscriptionService = $subscriptionService;
        $this->feeSettlementService = $feeSettlementService;
        $this->fineSettlementService = $fineSettlementService;
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
                'settlements' => [
                    'current' => $this->feeSettlementService->getSettlementCount($session, false),
                    'previous' => $this->feeSettlementService->getSettlementCount($session, true),
                ],
                'bills' => [
                    'current' => $this->feeSettlementService->getBillCount($session, false),
                    'previous' => $this->feeSettlementService->getBillCount($session, true),
                ],
                'zero' => $this->feeSettlementService->getFormattedAmount(0),
                'summary' => $this->meetingService->getFeesSummary($session),
            ],
            'fines' => [
                'session' => $session,
                'fines' => $this->meetingService->getFines($session),
                'settlements' => [
                    'current' => $this->fineSettlementService->getSettlementCount($session, false),
                    'previous' => $this->fineSettlementService->getSettlementCount($session, true),
                ],
                'bills' => [
                    'current' => $this->fineSettlementService->getBillCount($session, false),
                    'previous' => $this->fineSettlementService->getBillCount($session, true),
                ],
                'zero' => $this->fineSettlementService->getFormattedAmount(0),
                'summary' => $this->meetingService->getFinesSummary($session),
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
