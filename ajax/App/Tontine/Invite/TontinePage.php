<?php

namespace Ajax\App\Tontine\Invite;

use Ajax\PageComponent;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Tontine\TontineService;
use Stringable;

/**
 * @databag tontine
 */
class TontinePage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'invite.page'];

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
        $tontines = $this->tontineService->getGuestTontines($this->page);
        [$countries, $currencies] = $this->localeService->getNamesFromTontines($tontines);

        return $this->renderView('pages.tontine.invite.page', [
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
