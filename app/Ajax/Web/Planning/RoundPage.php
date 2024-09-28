<?php

namespace App\Ajax\Web\Planning;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Planning\RoundService;

/**
 * @databag tontine
 */
class RoundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'round.page'];

    /**
     * @param RoundService $roundService
     */
    public function __construct(protected RoundService $roundService)
    {}

    public function html(): string
    {
        return (string)$this->renderView('pages.planning.round.page', [
            'rounds' => $this->roundService->getRounds($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->roundService->getRoundCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-page-rounds');

        return $this->response;
    }
}
