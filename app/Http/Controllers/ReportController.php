<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HeadlessChromium\Browser;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;
use Siak\Tontine\Service\FeeSettlementService;
use Siak\Tontine\Service\FineSettlementService;
use Siak\Tontine\Service\BiddingService;
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
     * @param int $fundId
     *
     * @return View
     */
    public function fund(Request $request, Browser $browser,
        MeetingService $meetingService, SubscriptionService $subscriptionService, int $fundId)
    {
        $fund = $subscriptionService->getFund($fundId);
        view()->share($meetingService->getFigures($fund));

        $html = view('report.fund', [
            'tontine' => $meetingService->getTontine(),
            'fund' => $fund,
            'funds' > $subscriptionService->getFunds(),
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
        FineSettlementService $fineSettlementService, int $sessionId)
    {
        $tontine = $meetingService->getTontine();
        $session = $meetingService->getSession($sessionId);

        $html = view('report.session', [
            'tontine' => $tontine,
            'session' => $session,
            'receivables' => $meetingService->getFundsWithReceivables($session),
            'payables' => $meetingService->getFundsWithPayables($session),
            'summary' => $meetingService->getFundsSummary($session),
            'fees' => [
                'tontine' => $tontine,
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
                'tontine' => $tontine,
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
