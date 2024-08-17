<?php

namespace App\Ajax\Web\Tontine\Guest;

use App\Ajax\CallableClass;
use App\Ajax\Web\Tontine\Select;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\GuestService;
use Siak\Tontine\Service\Tontine\TontineService;

use function Jaxon\jq;

/**
 * @databag tontine
 */
class Tontine extends CallableClass
{
    /**
     * @param LocaleService $localeService
     * @param GuestService $guestService
     * @param TontineService $tontineService
     */
    public function __construct(private LocaleService $localeService,
        private GuestService $guestService, private TontineService $tontineService)
    {}

    public function home()
    {
        $this->response->html('guest-tontine-home', $this->renderView('pages.tontine.guest.home'));
        $this->jq('#btn-guest-tontine-refresh')->click($this->rq()->home());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $tontineCount = $this->tontineService->getGuestTontineCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $tontineCount, 'tontine', 'guest.page');
        $tontines = $this->tontineService->getGuestTontines($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $tontineCount);
        [$countries, $currencies] = $this->localeService->getNamesFromTontines($tontines);

        $html = $this->renderView('pages.tontine.guest.page', [
            'tontines' => $tontines,
            'countries' => $countries,
            'currencies' => $currencies,
            'pagination' => $pagination,
        ]);
        $this->response->html('guest-tontine-page', $html);
        $this->response->call('makeTableResponsive', 'guest-tontine-page');

        $tontineId = jq()->parent()->attr('data-tontine-id')->toInt();
        $this->jq('.btn-guest-tontine-choose')->click($this->rq(Select::class)->saveTontine($tontineId));

        return $this->response;
    }
}
