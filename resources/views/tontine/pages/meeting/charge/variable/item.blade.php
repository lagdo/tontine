                        <tr>
                          <td>
                            {{ $charge->name }}<br/>
                            {{ $charge->has_amount ? $charge->money('amount') : __('tontine.labels.fees.variable') }}
                          </td>
                          <td>
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $charge->currentBillCount }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $charge->previousBillCount }}
                          </td>
                          <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-fine-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-fine-add',
    'text' => $charge->is_fee ? __('common.actions.ask') : __('common.actions.give'),
  ],[
    'class' => 'btn-fine-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
                          </td>
                        </tr>