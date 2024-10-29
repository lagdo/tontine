<?php

namespace App\Ajax;

use App\Ajax\Web\Pagination;
use Jaxon\Plugin\Response\Pagination\Paginator;
use Jaxon\Response\ComponentResponse;

abstract class PageComponent extends Component
{
    /**
     * The current page number.
     *
     * @var int
     */
    protected int $page = 1;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = [];

    /**
     * Set the page number.
     *
     * @param int $pageNumber
     *
     * @return void
     */
    protected function setPageNumber(int $pageNumber): void
    {
        $bagName = $this->bagOptions[0];
        $attrName = $this->bagOptions[1] ?? 'page';
        $this->bag($bagName)->set($attrName, $pageNumber);

        $this->page = $pageNumber;
    }

    /**
     * Get the page number.
     *
     * @param int $pageNumber
     *
     * @return int
     */
    protected function getPageNumber(int $pageNumber): int
    {
        $bagName = $this->bagOptions[0];
        $attrName = $this->bagOptions[1] ?? 'page';

        return $pageNumber > 0 ? $pageNumber : (int)$this->bag($bagName)->get($attrName, 1);
    }

    /**
     * Get the total number of items to paginate.
     *
     * @return int
     */
    abstract protected function count(): int;

    /**
     * Render a page, and return a paginator for the component.
     *
     * @param int $pageNumber
     *
     * @return Paginator
     */
    protected function renderPage(int $pageNumber): Paginator
    {
        return $this->cl(Pagination::class)
            // Use the js class name as component item identifier.
            ->item($this->rq()->_class())
            ->pageNumber($this->getPageNumber($pageNumber))
            ->totalItems($this->count())
            ->itemsPerPage($this->tenantService->getLimit())
            ->paginator()
            ->page(function(int $page) {
                $this->setPageNumber($page);
                // Render the page content.
                $this->render();
            });
    }

    /**
     * Render the page and pagination components
     *
     * @param int $pageNumber
     *
     * @return ComponentResponse|null
     */
    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        return $this->response;
    }
}
