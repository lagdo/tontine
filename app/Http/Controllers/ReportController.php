<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\Report\Pdf\PdfPrinterService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;
use Sqids\SqidsInterface;
use Exception;

use function base64_decode;
use function response;
use function trans;
use function view;

class ReportController extends Controller
{
    /**
     * @param TenantService $tenantService
     * @param SqidsInterface $sqids
     * @param ReportService $reportService
     * @param PdfPrinterService $printerService
     */
    public function __construct(private TenantService $tenantService, private SqidsInterface $sqids,
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
     * @param string $guildSqid
     *
     * @return bool
     */
    private function setGuild(string $guildSqid): bool
    {
        [$guildId] = $this->sqids->decode($guildSqid);
        $guild = $this->tenantService->getGuild($guildId);
        if($guild === null)
        {
            return false;
        }

        $this->tenantService->setGuild($guild);
        return true;
    }

    /**
     * @param Request $request
     * @param string $guildSqid
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function session(Request $request, string $guildSqid, string $sessionSqid)
    {
        if(!$this->setGuild($guildSqid))
        {
            throw new Exception(trans('tontine.report.errors.report.not_found'));
        }

        [$sessionId] = $this->sqids->decode($sessionSqid);
        $session = $this->tenantService->getSessionById($sessionId);
        view()->share($this->reportService->getSessionReport($session));

        $guild = $this->tenantService->guild();
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
     * @param string $guildSqid
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function savings(Request $request, string $guildSqid, string $sessionSqid)
    {
        if(!$this->setGuild($guildSqid))
        {
            throw new Exception(trans('tontine.report.errors.report.not_found'));
        }

        [$sessionId] = $this->sqids->decode($sessionSqid);
        $session = $this->tenantService->getSessionById($sessionId);
        view()->share($this->reportService->getSavingsReport($session));

        $guild = $this->tenantService->guild();
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
     * @param string $guildSqid
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function credit(Request $request, string $guildSqid, string $sessionSqid)
    {
        if(!$this->setGuild($guildSqid))
        {
            throw new Exception(trans('tontine.report.errors.report.not_found'));
        }

        [$sessionId] = $this->sqids->decode($sessionSqid);
        $session = $this->tenantService->getSessionById($sessionId);
        view()->share($this->reportService->getCreditReport($session));

        $guild = $this->tenantService->guild();
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
     * @param string $guildSqid
     * @param string $roundSqid
     *
     * @return View|Response
     */
    public function round(Request $request, string $guildSqid, string $roundSqid)
    {
        if(!$this->setGuild($guildSqid))
        {
            throw new Exception(trans('tontine.report.errors.report.not_found'));
        }

        [$roundId] = $this->sqids->decode($roundSqid);
        $round = $this->tenantService->getRoundById($roundId);
        view()->share($this->reportService->getRoundReport($round));

        $guild = $this->tenantService->guild();
        view()->share('guild', $guild);

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
}
