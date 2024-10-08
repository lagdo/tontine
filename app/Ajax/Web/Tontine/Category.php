<?php

namespace App\Ajax\Web\Tontine;

use App\Ajax\CallableClass;
use Siak\Tontine\Model\Category as CategoryModel;
use Siak\Tontine\Service\Tontine\CategoryService;

use function Jaxon\jq;
use function Jaxon\pm;
use function trans;

/**
 * @databag tontine
 */
class Category extends CallableClass
{
    /**
     * @param CategoryService $categoryService
     */
    public function __construct(protected CategoryService $categoryService)
    {}

    /**
     * @exclude
     */
    public function show()
    {
        return $this->home();
    }

    public function home()
    {
        $html = $this->renderView('pages.options.category.home');
        $this->response->html('content-categories-home', $html);

        $this->jq('#btn-category-refresh')->click($this->rq()->home());
        $this->jq('#btn-category-create')->click($this->rq()->add());

        return $this->page();
    }

    public function page(int $pageNumber = 0)
    {
        $categoryCount = $this->categoryService->getCategoryCount();
        [$pageNumber, $perPage] = $this->pageNumber($pageNumber, $categoryCount,
            'tontine', 'category.page');
        $categories = $this->categoryService->getCategories($pageNumber);
        $pagination = $this->rq()->page()->paginate($pageNumber, $perPage, $categoryCount);

        $html = $this->renderView('pages.options.category.page', [
            'categories' => $categories,
            'pagination' => $pagination,
        ]);
        $this->response->html('category-page', $html);
        $this->response->call('makeTableResponsive', 'category-page');

        $categoryId = jq()->parent()->attr('data-category-id')->toInt();
        $this->jq('.btn-category-edit')->click($this->rq()->edit($categoryId));
        $this->jq('.btn-category-toggle')->click($this->rq()->toggle($categoryId));
        $this->jq('.btn-category-delete')->click($this->rq()->delete($categoryId)
            ->confirm(trans('tontine.category.questions.delete')));

        return $this->response;
    }

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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function create(array $formValues)
    {
        $this->categoryService->createCategory($formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.category.messages.created'), trans('common.titles.success'));

        return $this->response;
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
        $this->dialog->show($title, $content, $buttons);

        return $this->response;
    }

    public function update(int $categoryId, array $formValues)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->updateCategory($category, $formValues);
        $this->page(); // Back to current page

        $this->dialog->hide();
        $this->notify->success(trans('tontine.category.messages.updated'), trans('common.titles.success'));

        return $this->response;
    }

    public function toggle(int $categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->toggleCategory($category);

        return $this->page();
    }

    public function delete(int $categoryId)
    {
        $category = $this->categoryService->getCategory($categoryId);
        $this->categoryService->deleteCategory($category);

        return $this->page();
    }
}
