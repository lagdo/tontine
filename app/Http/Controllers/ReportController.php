<?php

namespace App\Http\Controllers;

use App\Facades\PdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;

use function base64_decode;
use function view;
use function response;

class ReportController extends Controller
{
    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * @param TenantService $tenantService
     * @param ReportService $reportService
     */
    public function __construct(TenantService $tenantService, ReportService $reportService)
    {
        $this->tenantService = $tenantService;
        $this->reportService = $reportService;
    }

    /**
     * @param Request $request
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function session(Request $request, int $sessionId)
    {
        $session = $this->tenantService->getSession($sessionId);
        $html = view('tontine.report.session', $this->reportService->getSessionReport($session));
        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        $filename = $this->reportService->getSessionReportFilename($session);
        return response(base64_decode(PdfGenerator::getPdf("$html")), 200)
            ->header('Content-Description', 'Session Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=$filename")
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }

    /**
     * @param Request $request
     * @param int $roundId
     *
     * @return View|Response
     */
    public function round(Request $request, int $roundId)
    {
        $round = $this->tenantService->tontine()->rounds()->find($roundId);
        $html = view('tontine.report.round', $this->reportService->getRoundReport($round));
        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        $filename = $this->reportService->getRoundReportFilename($round);
        return response(base64_decode(PdfGenerator::getPdf("$html")), 200)
            ->header('Content-Description', 'Round Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=$filename")
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }
}
