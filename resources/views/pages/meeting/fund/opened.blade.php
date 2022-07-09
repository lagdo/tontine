                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-fund-id',
  'dataIdValue' => $fund->id,
  'menus' => [[
    'class' => $menuClass,
    'text' => $menuText,
  ]],
])
                          </td>
                        </tr>
