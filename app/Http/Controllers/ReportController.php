<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\Guild\RoundService;
use Siak\Tontine\Service\Guild\SessionService;
use Siak\Tontine\Service\Report\Pdf\PdfPrinterService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;
use Sqids\SqidsInterface;

use function base64_decode;
use function response;
use function trans;
use function view;

class ReportController extends Controller
{
    /**
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param RoundService $roundService
     * @param ReportService $reportService
     * @param PdfPrinterService $printerService
     */
    public function __construct(private TenantService $tenantService,
        private SessionService $sessionService, private RoundService $roundService,
        private ReportService $reportService, private PdfPrinterService $printerService)
    {}

    /**
     * @param string $content
     * @param string $filename
     * @param string $title
     *
     * @return View|Response
     */
    private function pdfContent(string $content, string $filename, string $title)
    {
        return response(base64_decode($content), 200)
            ->header('Content-Description', $title)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "inline; filename=$filename")
            ->header('Content-Transfer-Encoding', 'binary')
            ->header('Expires', '0')
            ->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
            ->header('Pragma', 'public');
    }

    /**
     * @param Request $request
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function sessionById(Request $request, int $sessionId)
    {
        $guild = $this->tenantService->guild();
        $session = $this->sessionService->getSession($guild, $sessionId);
        view()->share($this->reportService->getSessionReport($session));
        // Show the html page
        if($request->has('html'))
        {
            return view($this->printerService->getSessionReportPath($guild));
        }

        // Print the pdf
        return $this->pdfContent($this->printerService->getSessionReport($guild),
            $this->printerService->getSessionReportFilename($session),
            trans('tontine.report.titles.session'));
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
    public function savings(Request $request, SqidsInterface $sqids, string $sessionSqid)
    {
        $guild = $this->tenantService->guild();
        [$sessionId] = $sqids->decode($sessionSqid);
        $session = $this->sessionService->getSession($guild, $sessionId);
        view()->share($this->reportService->getSavingsReport($session));

        // Show the html page
        if($request->has('html'))
        {
            return view($this->printerService->getSavingsReportPath($guild));
        }

        // Print the pdf
        return $this->pdfContent($this->printerService->getSavingsReport($guild),
            $this->printerService->getSavingsReportFilename($session),
            trans('tontine.report.titles.savings'));
    }

    /**
     * @param Request $request
     * @param SqidsInterface $sqids
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function credit(Request $request, SqidsInterface $sqids, string $sessionSqid)
    {
        $guild = $this->tenantService->guild();
        [$sessionId] = $sqids->decode($sessionSqid);
        $session = $this->sessionService->getSession($guild, $sessionId);
        view()->share($this->reportService->getCreditReport($session));

        // Show the html page
        if($request->has('html'))
        {
            return view($this->printerService->getCreditReportPath($guild));
        }

        // Print the pdf
        return $this->pdfContent($this->printerService->getCreditReport($guild),
            $this->printerService->getCreditReportFilename($session),
            trans('tontine.report.titles.credit'));
    }

    /**
     * @param Request $request
     * @param int $roundId
     *
     * @return View|Response
     */
    public function roundById(Request $request, int $roundId)
    {
        $guild = $this->tenantService->guild();
        $round = $this->roundService->getRound($guild, $roundId);
        view()->share($this->reportService->getRoundReport($round));

        // Show the html page
        if($request->has('html'))
        {
            return view($this->printerService->getRoundReportPath($guild));
        }

        // Print the pdf
        return $this->pdfContent($this->printerService->getRoundReport($guild),
            $this->printerService->getRoundReportFilename($round),
            trans('tontine.report.titles.round'));
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
