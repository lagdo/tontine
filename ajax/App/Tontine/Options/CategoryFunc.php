<?php

namespace Ajax\App\Tontine\Options;

use Ajax\FuncComponent;
use Siak\Tontine\Model\Category as CategoryModel;
use Siak\Tontine\Service\Tontine\CategoryService;

use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class CategoryFunc extends FuncComponent
{
    /**
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {}

    public function add()
    {
        $types = [
            CategoryModel::TYPE_DISBURSEMENT => trans('tontine.category.types.disbursement'),
        ];
        $title = trans('tontine.category.titles.add');
        $content = $this->renderView('pages.options.category.add', [
            'types' => $types,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->create(pm()->form('category-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function create(array $formValues)
    {
        $this->categoryService->createCategory($formValues);
        $this->cl(CategoryPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.category.messages.created'));
    }

    public function edit(int $categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);

        $title = trans('tontine.category.titles.edit');
        $types = [
            CategoryModel::TYPE_DISBURSEMENT => trans('tontine.category.types.disbursement'),
        ];
        $content = $this->renderView('pages.options.category.edit', [
            'types' => $types,
            'category' => $category,
        ]);
        $buttons = [[
            'title' => trans('common.actions.cancel'),
            'class' => 'btn btn-tertiary',
            'click' => 'close',
        ],[
            'title' => trans('common.actions.save'),
            'class' => 'btn btn-primary',
            'click' => $this->rq()->update($category->id, pm()->form('category-form')),
        ]];
        $this->modal()->show($title, $content, $buttons);
    }

    public function update(int $categoryId, array $formValues)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->updateCategory($category, $formValues);
        $this->cl(CategoryPage::class)->page(); // Back to current page

        $this->modal()->hide();
        $this->alert()->title(trans('common.titles.success'))
            ->success(trans('tontine.category.messages.updated'));
    }

    public function toggle(int $categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->toggleCategory($category);

        $this->cl(CategoryPage::class)->page();
    }

    public function delete(int $categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->deleteCategory($category);

        $this->cl(CategoryPage::class)->page();
    }
}
