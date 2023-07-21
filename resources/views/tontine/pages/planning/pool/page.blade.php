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
                          <td class="currency">{{ $tontine->is_libre ?
                            __('tontine.labels.types.libre') : $pool->money('amount') }}</td>
                          <td class="table-item-menu">
@if (!$tontine->is_libre)
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
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
{!! $pagination !!}
