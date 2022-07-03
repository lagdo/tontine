                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>[{{ $charge->members_paid }}/{{ $charge->members_count }}]</td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => [[
    'class' => 'btn-charge-settlements',
    'text' => __('meeting.actions.settlements'),
  ]],
])
                          </td>
                        </tr>
