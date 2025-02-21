<?php

namespace Ajax;

use Jaxon\App\PageComponent as BaseComponent;
use Jaxon\App\PageDatabagTrait;
use Jaxon\Response\AjaxResponse;

abstract class PageComponent extends BaseComponent
{
    use ComponentTrait;
    use PageDatabagTrait;

    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = [];

    /**
     * @inheritDoc
     */
    protected function bagName(): string
    {
        return (string)$this->bagOptions[0];
    }

    /**
     * @inheritDoc
     */
    protected function bagAttr(): string
    {
        return (string)($this->bagOptions[1] ?? 'page');
    }

    /**
     * @inheritDoc
     */
    protected function limit(): int
    {
        return $this->tenantService->getLimit();
    }

    /**
     * Render the page and pagination components
     *
     * @param int $pageNumber
     *
     * @return AjaxResponse|null
     */
    public function page(int $pageNumber = 0): ?AjaxResponse
    {
        // Get the paginator. This will also set the current page number value.
        $paginator = $this->paginator($pageNumber);
        // Render the page content.
        $this->render();
        // Render the pagination component.
        $paginator->render($this->rq()->page());

        return $this->response;
    }
}
