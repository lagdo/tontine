@php
  $categoryId = jq()->parent()->attr('data-category-id')->toInt();
  $rqCategoryFunc = rq(Ajax\App\Tontine\Options\CategoryFunc::class);
  $rqCategoryPage = rq(Ajax\App\Tontine\Options\CategoryPage::class);
@endphp
                  <div class="table-responsive" id="content-category-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-category-edit', 'click'], $rqCategoryFunc->edit($categoryId))></div>
                    <div @jxnEvent(['.btn-category-toggle', 'click'], $rqCategoryFunc->toggle($categoryId))></div>
                    <div @jxnEvent(['.btn-category-delete', 'click'], $rqCategoryFunc->delete($categoryId)
                      ->confirm(__('tontine.category.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th class="table-item-menu">{!! __('common.labels.active') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($categories as $category)
                        <tr>
                          <td>{{ $category->name }}</td>
                          <td class="table-item-toggle" data-category-id="{{ $category->id }}">
                            <a role="link" tabindex="0" class="btn-category-toggle"><i class="fa fa-toggle-{{ $category->active ? 'on' : 'off' }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-category-id',
  'dataIdValue' => $category->id,
  'menus' => [[
    'class' => 'btn-category-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-category-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqCategoryPage)>
                    </nav>
                  </div> <!-- End table -->
