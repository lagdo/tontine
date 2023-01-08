<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HeadlessChromium\Browser;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\LoanService;
use Siak\Tontine\Service\RefundService;

use function config;
use function view;
use function response;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @param Browser $browser
     * @param MeetingService $meetingService
     * @param SubscriptionService $subscriptionService
     * @param int $poolId
     *
     * @return View
     */
    public function pool(Request $request, Browser $browser,
        MeetingService $meetingService, SubscriptionService $subscriptionService, int $poolId)
    {
        $pool = $subscriptionService->getPool($poolId);
        view()->share($meetingService->getFigures($pool));

        $html = view('report.pool', [
            'tontine' => $meetingService->getTontine(),
            'pool' => $pool,
            'pools' > $subscriptionService->getPools(),
        ]);

        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        try
        {
            $page = $browser->createPage();
            $page->setHtml("$html");
            $pdf = $page->pdf(config('chrome.page', []));

            return response(base64_decode($pdf->getBase64()), 200)
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename=report.pdf')
                ->header('Content-Transfer-Encoding', 'binary')
                ->header('Expires', '0')
                ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->header('Pragma', 'public');
        }
        finally
        {
            $browser->close();
        }
    }

    /**
     * @param MeetingService $meetingService
     * @param int $sessionId
     *
     * @return View
     */
    public function session(Request $request, Browser $browser,
        MeetingService $meetingService, FeeSettlementService $feeSettlementService,
        FineSettlementService $fineSettlementService, LoanService $loanService,
        RefundService $refundService, int $sessionId)
    {
        $tontine = $meetingService->getTontine();
        $session = $meetingService->getSession($sessionId);
        $summary = $meetingService->getPoolsSummary($session);

        $html = view('report.session', [
            'tontine' => $tontine,
            'session' => $session,
            'deposits' => [
                'session' => $session,
                'pools' => $meetingService->getPoolsWithReceivables($session),
                'summary' => $summary['receivables'],
                'sum' => $summary['sum']['receivables'],
            ],
            'remitments' => [
                'session' => $session,
                'pools' => $meetingService->getPoolsWithPayables($session),
                'summary' => $summary['payables'],
                'sum' => $summary['sum']['payables'],
            ],
            'fees' => [
                'session' => $session,
                'fees' => $meetingService->getFees($session),
                'settlements' => [
                    'current' => $feeSettlementService->getSettlementCount($session, false),
                    'previous' => $feeSettlementService->getSettlementCount($session, true),
                ],
                'bills' => [
                    'current' => $feeSettlementService->getBillCount($session, false),
                    'previous' => $feeSettlementService->getBillCount($session, true),
                ],
                'zero' => $feeSettlementService->getFormattedAmount(0),
                'summary' => $meetingService->getFeesSummary($session),
            ],
            'fines' => [
                'session' => $session,
                'fines' => $meetingService->getFines($session),
                'settlements' => [
                    'current' => $fineSettlementService->getSettlementCount($session, false),
                    'previous' => $fineSettlementService->getSettlementCount($session, true),
                ],
                'bills' => [
                    'current' => $fineSettlementService->getBillCount($session, false),
                    'previous' => $fineSettlementService->getBillCount($session, true),
                ],
                'zero' => $fineSettlementService->getFormattedAmount(0),
                'summary' => $meetingService->getFinesSummary($session),
            ],
        ]);

        /*if($tontine->is_financial)
        {
            [$loans, $sum] = $loanService->getSessionLoans($session);
            $amountAvailable = $loanService->getAmountAvailable($session);
            $html->with('loans', [
                'session' => $session,
                'loans' => $loans,
                'sum' => $sum,
                'amountAvailable' => Currency::format($amountAvailable),
            ]);
            $html->with('refunds', [
                'session' => $session,
                'loans' => $refundService->getLoans($session, true),
                'refundSum' => $refundService->getRefundSum($session),
            ]);
        }*/

        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        try
        {
            $page = $browser->createPage();
            $page->setHtml("$html");
            $pdf = $page->pdf(config('chrome.page', []));

            return response(base64_decode($pdf->getBase64()), 200)
                ->header('Content-Description', 'File Transfer')
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename=report.pdf')
                ->header('Content-Transfer-Encoding', 'binary')
                ->header('Expires', '0')
                ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                ->header('Pragma', 'public');
        }
        finally
        {
            $browser->close();
        }
    }
}
