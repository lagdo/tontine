<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Category;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Service\TenantService;

class AccountService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of categories.
     *
     * @param Guild $guild
     * @param int $page
     *
     * @return Collection
     */
    public function getAccounts(Guild $guild, int $page = 0): Collection
    {
        return $guild->categories()
            ->outflow()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of categories.
     *
     * @param Guild $guild
     *
     * @return int
     */
    public function getCategoryCount(Guild $guild): int
    {
        return $guild->categories()->outflow()->count();
    }

    /**
     * Get a single category.
     *
     * @param Guild $guild
     * @param int $categoryId
     *
     * @return Category|null
     */
    public function getAccount(Guild $guild, int $categoryId): ?Category
    {
        return $guild->categories()->outflow()->find($categoryId);
    }

    /**
     * Add new category.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createAccount(Guild $guild, array $values): bool
    {
        $guild->categories()->create($values);
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
    public function updateAccount(Category $category, array $values): bool
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
    public function toggleAccount(Category $category)
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
    public function deleteAccount(Category $category)
    {
        $category->delete();
    }
}
