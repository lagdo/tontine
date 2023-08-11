<?php

namespace App\Http\Controllers;

use App\Facades\PdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\Report\ReportService;

use function base64_decode;
use function view;
use function response;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @param ReportService $reportService
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function session(Request $request, ReportService $reportService, int $sessionId)
    {
        $html = view('tontine.report.session', $reportService->getSessionReport($sessionId));
        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        return response(base64_decode(PdfGenerator::getPdf("$html")), 200)
            ->header('Content-Description', 'Session Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=report.pdf')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }

    /**
     * @param Request $request
     * @param ReportService $reportService
     * @param int $roundId
     *
     * @return View|Response
     */
    public function round(Request $request, ReportService $reportService, int $roundId)
    {
        $html = view('tontine.report.round', $reportService->getRoundReport($roundId));
        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        return response(base64_decode(PdfGenerator::getPdf("$html")), 200)
            ->header('Content-Description', 'Round Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=report.pdf')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }
}
