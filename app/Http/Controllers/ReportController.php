<?php

namespace App\Http\Controllers;

use App\Facades\PdfGenerator;
use App\Facades\Report;
use Illuminate\Http\Request;

use function base64_decode;
use function view;
use function response;

class ReportController extends Controller
{
    /**
     * @param Request $request
     * @param int $poolId
     *
     * @return View|Response
     */
    public function pool(Request $request, int $poolId)
    {
        $html = view('tontine.report.pool', Report::getPoolReport($poolId));

        // Show the html page
        if($request->has('html'))
        {
            return $html;
        }

        // Print the pdf
        return response(base64_decode(PdfGenerator::getPdf("$html")), 200)
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
     * @param int $sessionId
     *
     * @return View|Response
     */
    public function session(Request $request, int $sessionId)
    {
        $html = view('tontine.report.session', Report::getSessionReport($sessionId));

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
}
