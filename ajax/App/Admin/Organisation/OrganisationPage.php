<?php

namespace Ajax\App\Admin\Organisation;

use Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\TontineService;
use Stringable;

/**
 * @databag tontine
 * @databag pool
 */
class OrganisationPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'page'];

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
        return $this->tontineService->getTontineCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        $tontines = $this->tontineService->getTontines($this->pageNumber());
        [$countries, $currencies] = $this->localeService->getNamesFromTontines($tontines);

        return $this->renderView('pages.tontine.page', [
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
