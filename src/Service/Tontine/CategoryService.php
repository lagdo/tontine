<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Service\TenantService;

class CategoryService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of categories.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getCategories(int $page = 0): Collection
    {
        return $this->tenantService->tontine()
            ->categories()
            ->disbursement()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of categories.
     *
     * @return int
     */
    public function getCategoryCount(): int
    {
        return $this->tenantService->tontine()->categories()->disbursement()->count();
    }

    /**
     * Get a single category.
     *
     * @param int $categoryId    The category id
     *
     * @return Category|null
     */
    public function getCategory(int $categoryId): ?Category
    {
        return $this->tenantService->tontine()->categories()->disbursement()->find($categoryId);
    }

    /**
     * Add new category.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createCategory(array $values): bool
    {
        $this->tenantService->tontine()->categories()->create($values);
        return true;
    }

    /**
     * Update a category.
     *
     * @param Category $category
     * @param array $values
     *
     * @return bool
     */
    public function updateCategory(Category $category, array $values): bool
    {
        return $category->update($values);
    }

    /**
     * Toggle a category.
     *
     * @param Category $category
     *
     * @return void
     */
    public function toggleCategory(Category $category)
    {
        $category->update(['active' => !$category->active]);
    }

    /**
     * Delete a category.
     *
     * @param Category $category
     *
     * @return void
     */
    public function deleteCategory(Category $category)
    {
        $category->delete();
    }
}
