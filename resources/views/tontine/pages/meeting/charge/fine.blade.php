                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $bills['total']['current'][$charge->id] ?? 0 }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $bills['total']['previous'][$charge->id] ?? 0 }}
                          </td>
                          <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-fine-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-fine-add',
    'text' => __('common.actions.give'),
  ],[
    'class' => 'btn-fine-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
                          </td>
                        </tr>
