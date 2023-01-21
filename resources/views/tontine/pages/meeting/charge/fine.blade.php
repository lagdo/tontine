                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->paid_bills_count }}/{{ $charge->bills_count }}<br/>
                            {{ $charge->all_paid_bills_count }}/{{ $charge->all_bills_count }}
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
