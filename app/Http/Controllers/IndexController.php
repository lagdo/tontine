<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Ajax\App\Meeting\Meeting;
use App\Ajax\App\Meeting\Report as MeetingReport;
use App\Ajax\App\Planning\Pool;
use App\Ajax\App\Planning\Report as PlanningReport;
use App\Ajax\App\Planning\Session as Planning;
use App\Ajax\App\Tontine\Charge;
use App\Ajax\App\Tontine\Member;
use App\Ajax\App\Tontine\Round;
use App\Ajax\App\Tontine\Tontine;
use Siak\Tontine\Service\Tontine\TenantService;
use Siak\Tontine\Service\Tontine\TontineService;
use Jaxon\Laravel\Jaxon;

use function auth;
use function view;

class IndexController extends Controller
{
    /**
     * Show the home page.
     *
     * @param TenantService $tenantService
     * @param TontineService $tontineService
     * @param Jaxon $jaxon
     *
     * @return View
     */
    public function index(TenantService $tenantService, TontineService $tontineService, Jaxon $jaxon): View
    {
        // Localized Jaxon request processing URI
        $jaxon->setOption('core.request.uri', LaravelLocalization::localizeUrl('/ajax'));
        // Requests to Jaxon classes
        $jxnRound = $jaxon->request(Round::class);
        $jxnTontine = $jaxon->request(Tontine::class);

        view()->share([
            'user' => auth()->user(),
            'tontine' => $tenantService->tontine(),
            'round' => $tenantService->round(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
            // Jaxon callables
            'jxnMember' => $jaxon->request(Member::class),
            'jxnCharge' => $jaxon->request(Charge::class),
            'jxnPlanning' => $jaxon->request(Planning::class),
            'jxnPool' => $jaxon->request(Pool::class),
            'jxnPlanningReport' => $jaxon->request(PlanningReport::class),
            'jxnMeeting' => $jaxon->request(Meeting::class),
            'jxnMeetingReport' => $jaxon->request(MeetingReport::class),
            'jxnTontine' => $jxnTontine,
            'jxnRound' => $jxnRound,
            'tontines' => $tontineService->getTontines(1),
            'pagination' => $jxnTontine->page()->paginate(1, 10, $tontineService->getTontineCount()),
        ]);

        return view('tontine.base.home', [
            'pageTitle' => "Siak Tontine",
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
    }
}
