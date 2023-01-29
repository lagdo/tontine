<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Report\PdfGeneratorInterface;
use Siak\Tontine\Service\Report\ReportServiceInterface;

use function base64_decode;
use function view;
use function response;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @param PdfGeneratorInterface $pdfGenerator
     * @param ReportServiceInterface $reportService
     * @param int $poolId
     *
     * @return View|Response
     */
    public function pool(Request $request, PdfGeneratorInterface $pdfGenerator,
        ReportServiceInterface $reportService, int $poolId)
    {
        $html = view('tontine.report.pool', $reportService->getPoolReport($poolId));

        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        return response(base64_decode($pdfGenerator->getPdf("$html")), 200)
            ->header('Content-Description', 'Pool Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=report.pdf')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }

    /**
     * @param Request $request
     * @param PdfGeneratorInterface $pdfGenerator
     * @param ReportServiceInterface $reportService
     * @param LocaleService $localeService
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function session(Request $request, PdfGeneratorInterface $pdfGenerator,
        ReportServiceInterface $reportService, LocaleService $localeService, int $sessionId)
    {
        view()->share(['zero' => $localeService->formatMoney(0)]);
        $html = view('tontine.report.session', $reportService->getSessionReport($sessionId));

        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        return response(base64_decode($pdfGenerator->getPdf("$html")), 200)
            ->header('Content-Description', 'Session Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename=report.pdf')
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }
}
