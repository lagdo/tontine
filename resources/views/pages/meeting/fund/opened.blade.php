                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $fund->recv_paid }}/{{ $fund->recv_count }}<br/>{{ $fund->pay_paid }}/{{ $fund->pay_count }}</td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => [[
    'class' => 'btn-fund-deposits',
    'text' => __('meeting.actions.deposits'),
  ],[
    'class' => $tontine->is_mutual ? 'btn-mutual-remittances' : 'btn-financial-remittances',
    'text' => __('meeting.actions.remittances'),
  ]],
])
                          </td>
                        </tr>
