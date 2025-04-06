<?php

namespace Ajax\App\Guild\Options;

use Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\CategoryService;
use Stringable;

/**
 * @databag tontine
 */
class CategoryPage extends PageComponent
{
    /**
     * The pagination databag options
     *
     * @var array
     */
    protected array $bagOptions = ['tontine', 'category.page'];

    /**
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {}

    /**
     * @inheritDoc
     */
    protected function count(): int
    {
        return $this->categoryService->getCategoryCount();
    }

    /**
     * @inheritDoc
     */
    public function html(): Stringable
    {
        return $this->renderView('pages.guild.options.category.page', [
            'categories' => $this->categoryService->getCategories($this->currentPage()),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js('Tontine')->makeTableResponsive('content-category-page');
    }
}
