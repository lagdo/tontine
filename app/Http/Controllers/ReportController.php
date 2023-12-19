<?php

namespace App\Http\Controllers;

use App\Facades\PdfGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;
use Sqids\SqidsInterface;

use function base64_decode;
use function config;
use function response;
use function view;

class ReportController extends Controller
{
    /**
     * @param TenantService $tenantService
     * @param ReportService $reportService
     */
    public function __construct(protected TenantService $tenantService,
        protected ReportService $reportService)
    {}

    /**
     * @param Request $request
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function sessionById(Request $request, int $sessionId)
    {
        $template = config('tontine.templates.report', 'default');
        $session = $this->tenantService->getSession($sessionId);
        view()->share($this->reportService->getSessionReport($session));
        // Show the html page
        if($request->has('html'))
        {
            return view("tontine.report.$template.session");
        }

        // Print the pdf
        $filename = $this->reportService->getSessionReportFilename($session);
        return response(base64_decode(PdfGenerator::getSessionReport($template)), 200)
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
     * @param SqidsInterface $sqids
     * @param string $sessionId
     *
     * @return View|Response
     */
    public function session(Request $request, SqidsInterface $sqids, string $sessionSqid)
    {
        [$sessionId] = $sqids->decode($sessionSqid);
        return $this->sessionById($request, $sessionId);
    }

    /**
     * @param Request $request
     * @param SqidsInterface $sqids
     * @param string $sessionId
     *
     * @return View|Response
     */
    public function profits(Request $request, SqidsInterface $sqids, string $sessionSqid)
    {
        [$sessionId] = $sqids->decode($sessionSqid);
        $template = config('tontine.templates.report', 'default');
        $session = $this->tenantService->getSession($sessionId);
        view()->share($this->reportService->getProfitsReport($session));
        // Show the html page
        if($request->has('html'))
        {
            return view("tontine.report.$template.profits");
        }

        // Print the pdf
        $filename = $this->reportService->getProfitsReportFilename($session);
        return response(base64_decode(PdfGenerator::getProfitsReport($template)), 200)
            ->header('Content-Description', 'Profits Report')
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
    public function roundById(Request $request, int $roundId)
    {
        $template = config('tontine.templates.report', 'default');
        $round = $this->tenantService->getRound($roundId);
        view()->share($this->reportService->getRoundReport($round));
        // Show the html page
        if($request->has('html'))
        {
            return view("tontine.report.$template.round");
        }

        // Print the pdf
        $filename = $this->reportService->getRoundReportFilename($round);
        return response(base64_decode(PdfGenerator::getRoundReport($template)), 200)
            ->header('Content-Description', 'Round Report')
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=$filename")
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }

    /**
     * @param Request $request
     * @param SqidsInterface $sqids
     * @param string $roundSqid
     *
     * @return View|Response
     */
    public function round(Request $request, SqidsInterface $sqids, string $roundSqid)
    {
        [$roundId] = $sqids->decode($roundSqid);
        return $this->roundById($request, $roundId);
    }
}
