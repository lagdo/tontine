<?php

namespace Ajax\App\Admin\User\Guest;

use Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\TontineService;
use Stringable;

/**
 * @databag user
 */
class OrganisationPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['user', 'tontine.page'];

    /**
     * @param LocaleService $localeService
     * @param TontineService $tontineService
     */
    public function __construct(private LocaleService $localeService,
        private TontineService $tontineService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->tontineService->getGuestTontineCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $tontines = $this->tontineService->getGuestTontines($this->currentPage());
        [$countries, $currencies] = $this->localeService->getNamesFromTontines($tontines);

        return $this->renderView('pages.user.guest.organisation.page', [
            'tontines' => $tontines,
            'countries' => $countries,
            'currencies' => $currencies,
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('content-page');
    }
}
