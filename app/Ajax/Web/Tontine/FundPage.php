<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\FundService;

/**
 * @databag tontine
 */
class FundPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'fund.page'];

    /**
     * @param FundService $fundService
     */
    public function __construct(protected FundService $fundService)
    {}

    public function html(): string
    {
        return (string)$this->renderView('pages.options.fund.page', [
            'funds' => $this->fundService->getFunds($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->fundService->getFundCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('fund-page');

        return $this->response;
    }
}
