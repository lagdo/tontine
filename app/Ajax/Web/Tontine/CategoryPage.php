<?php

namespace App\Ajax\Web\Tontine;

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

    public function html(): string
    {
        return (string)$this->renderView('pages.options.category.page', [
            'categories' => $this->categoryService->getCategories($this->page),
        ]);
    }

    protected function count(): int
    {
        return $this->categoryService->getCategoryCount();
    }

    public function page(int $pageNumber = 0)
    {
        // Render the page content.
        $this->renderPage($pageNumber)
            // Render the paginator.
            ->render($this->rq()->page());

        $this->response->js()->makeTableResponsive('category-page');

        return $this->response;
    }
}
