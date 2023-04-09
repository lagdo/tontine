                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($pools as $pool)
                        <tr>
                          <td>{{ $pool->title }}</td>
                          <td class="currency">{{ $pool->money('amount') }}</td>
                          <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-pool-id',
  'dataIdValue' => $pool->id,
  'menus' => [[
    'class' => 'btn-pool-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-pool-subscriptions',
    'text' => __('tontine.pool.actions.subscriptions'),
  ],[
    'class' => 'btn-pool-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
{!! $pagination !!}
