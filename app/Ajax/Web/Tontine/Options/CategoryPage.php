<?php

namespace App\Ajax\Web\Tontine\Options;

use App\Ajax\PageComponent;
use Siak\Tontine\Service\Tontine\CategoryService;

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
    public function html(): string
    {
        return (string)$this->renderView('pages.options.category.page', [
            'categories' => $this->categoryService->getCategories($this->page),
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function after()
    {
        $this->response->js()->makeTableResponsive('category-page');
    }
}
