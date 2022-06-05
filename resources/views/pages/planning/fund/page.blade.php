                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th class="currency">{!! __('common.labels.amount') !!}</th>
                          <th class="table-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($funds as $fund)
                        <tr>
                          <td>{{ $fund->title }}</td>
                          <td class="currency">{{ $fund->money('amount') }}</td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => [[
    'class' => 'btn-fund-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-fund-subscriptions',
    'text' => __('tontine.fund.actions.subscriptions'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
{!! $pagination !!}
