<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Meeting\SessionService;
use Siak\Tontine\Service\Report\Pdf\PdfPrinterService;
use Siak\Tontine\Service\Report\ReportService;
use Siak\Tontine\Service\TenantService;
use Sqids\SqidsInterface;

use function base64_decode;
use function compact;
use function response;
use function trans;
use function view;

class FormController extends Controller
{
    /**
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param PdfPrinterService $printerService
     */
    public function __construct(private TenantService $tenantService,
        private SessionService $sessionService, private PdfPrinterService $printerService)
    {}

    /**
     * @param Request $request
     * @param string $form
     *
     * @return View|Response
     */
    private function form(Request $request, string $form)
    {
        // Show the html page
        if($request->has('html'))
        {
            return view($this->printerService->getFormViewPath($form));
        }

        // Print the pdf
        $filename = $this->printerService->getFormFilename($form);
        return response(base64_decode($this->printerService->getEntryForm($form)), 200)
            ->header('Content-Description', trans("meeting.entry.titles.$form"))
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
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function session(Request $request, ReportService $reportService,
        SqidsInterface $sqids, string $sessionSqid)
    {
        [$sessionId] = $sqids->decode($sessionSqid);
        $session = $this->sessionService->getSession($sessionId);
        view()->share($reportService->getSessionEntry($session));

        return $this->form($request, 'session');
    }

    /**
     * @param Request $request
     * @param LocaleService $localeService
     * @param SqidsInterface $sqids
     * @param string $form
     * @param string $sessionSqid
     *
     * @return View|Response
     */
    public function entry(Request $request, LocaleService $localeService,
        SqidsInterface $sqids, string $form, string $sessionSqid = '')
    {
        $guild = $this->tenantService->guild();
        [$country] = $localeService->getNameFromGuild($guild);
        $session = null;
        if($sessionSqid !== '')
        {
            [$sessionId] = $sqids->decode($sessionSqid);
            $session = $this->sessionService->getSession($sessionId);
        }
        view()->share(compact('guild', 'country', 'session'));

        return $this->form($request, $form);
    }
}
