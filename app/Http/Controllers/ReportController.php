<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use HeadlessChromium\Browser;
use Siak\Tontine\Service\SubscriptionService;
use Siak\Tontine\Service\MeetingService;

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
     * @param SubscriptionService $subscriptionService
     * @param int $sessionId
     *
     * @return View
     */
    public function session(MeetingService $meetingService, SubscriptionService $subscriptionService, int $sessionId)
    {
        $session = $subscriptionService->getFund($sessionId);
        view()->share($meetingService->getFigures($session));

        return view('report.session', [
            'session' => $session,
            'sessions' > $subscriptionService->getFunds(),
        ]);
    }
}
