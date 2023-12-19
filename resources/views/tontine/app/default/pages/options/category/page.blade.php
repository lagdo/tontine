                    <table class="table table-bordered">
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
                          <td class="table-item-menu" data-category-id="{{ $category->id }}">
                            <a href="javascript:void(0)" class="btn-category-toggle"><i class="fa fa-toggle-{{ $category->active ? 'on' : 'off' }}"></i></a>
                          </td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
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
{!! $pagination !!}
