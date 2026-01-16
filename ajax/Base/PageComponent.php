<?php

namespace Ajax\Base;

use Jaxon\App\PageComponent as BaseComponent;
use Jaxon\App\PageDatabagTrait;
use Jaxon\Attributes\Attribute\Databag;

#[Databag('tenant')]
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
     * @return void
     */
    public function page(int $pageNumber = 0): void
    {
        $this->paginate($this->rq()->page(), $pageNumber);
    }
}
