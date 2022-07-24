<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

use App\Ajax\App\Charge;
use App\Ajax\App\Meeting\Session as Meeting;
use App\Ajax\App\Meeting\Table as CashFlow;
use App\Ajax\App\Member;
use App\Ajax\App\Planning\Session as Planning;
use App\Ajax\App\Planning\Fund;
use App\Ajax\App\Planning\Table;
use App\Ajax\App\Profile\Round;
use App\Ajax\App\Profile\Tontine;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\TontineService;
use Jaxon\Laravel\Jaxon;

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
     * @return Response
     */
    public function index(TenantService $tenantService, TontineService $tontineService, Jaxon $jaxon): Response
    {
        // Localized Jaxon request processing URI
        $jaxon->setOption('core.request.uri', LaravelLocalization::localizeUrl('/ajax'));
        // Requests to Jaxon classes
        $jxnRound = $jaxon->request(Round::class);
        $jxnTontine = $jaxon->request(Tontine::class);

        view()->share([
            'tontine' => $tenantService->tontine(),
            'round' => $tenantService->round(),
            'locales' => LaravelLocalization::getSupportedLocales(),
            'locale' => LaravelLocalization::getCurrentLocale(),
            'localeNative' => LaravelLocalization::getCurrentLocaleNative(),
            // Jaxon callables
            'jxnMember' => $jaxon->request(Member::class),
            'jxnCharge' => $jaxon->request(Charge::class),
            'jxnPlanning' => $jaxon->request(Planning::class),
            'jxnFund' => $jaxon->request(Fund::class),
            'jxnTable' => $jaxon->request(Table::class),
            'jxnMeeting' => $jaxon->request(Meeting::class),
            'jxnCashFlow' => $jaxon->request(CashFlow::class),
            'jxnTontine' => $jxnTontine,
            'jxnRound' => $jxnRound,
            'tontines' => $tontineService->getTontines(1),
            'pagination' => $jxnTontine->page()->paginate(1, 10, $tontineService->getTontineCount()),
        ]);

        return view('base.home', [
            'pageTitle' => "Siak Tontine",
            'jaxonCss' => $jaxon->css(),
            'jaxonJs' => $jaxon->js(),
            'jaxonScript' => $jaxon->script(),
        ]);
    }
}
