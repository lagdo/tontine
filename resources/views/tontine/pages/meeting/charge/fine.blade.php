                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->getCurrSettlementCount($settlements) }}/{{ $charge->getCurrBillCount($bills) }}<br/>
                            {{ $charge->getPrevSettlementCount($settlements) }}/{{ $charge->getPrevBillCount($bills) }}
                          </td>
                          <td class="table-item-menu">
@include('tontine.parts.table.menu', [
  'dataIdKey' => 'data-fine-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-fine-add',
    'text' => __('meeting.actions.fine'),
  ],[
    'class' => 'btn-fine-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
                          </td>
                        </tr>
