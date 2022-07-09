                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->getCurrSettlementCount($settlements) }}/{{ $charge->getCurrBillCount($bills) }}<br/>
                            {{ $charge->getPrevSettlementCount($settlements) }}/{{ $charge->getPrevBillCount($bills) }}
                          </td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-fee-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-fee-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
                          </td>
                        </tr>
