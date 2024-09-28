<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\ChargeService;

/**
 * @databag charge
 */
class ChargePage extends PageComponent
{
    use ChargeTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['charge', 'page'];

    /**
     * @param ChargeService $chargeService
     */
    public function __construct(protected ChargeService $chargeService)
    {}

    public function html(): string
    {
        return (string)$this->renderView('pages.options.charge.page', [
            'types' => $this->getChargeTypes(),
            'periods' => $this->getChargePeriods(),
            'charges' => $this->chargeService->getCharges($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->chargeService->getChargeCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('content-page');

        return $this->response;
    }
}
