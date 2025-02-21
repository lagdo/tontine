<?php

namespace Ajax\App\Planning\Calendar;

use Ajax\PageComponent;
use Siak\Tontine\Service\Planning\RoundService;
use Stringable;

/**
 * @databag planning
 */
class RoundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['planning', 'round.page'];

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->roundService->getRoundCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.planning.calendar.round.page', [
            'rounds' => $this->roundService->getRounds($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-planning-rounds-page');
    }
}
