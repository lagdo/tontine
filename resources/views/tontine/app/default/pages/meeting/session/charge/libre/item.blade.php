@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
  $menus = [[
    'class' => 'btn-fee-libre-add',
    'text' => __('common.actions.add'),
  ], [
    'class' => 'btn-fee-libre-settlements',
    'text' => __('meeting.actions.settlements'),
  ]];
  if($charge->is_fee && !$charge->has_amount)
  {
    $menus[] = [
      'class' => 'btn-fee-libre-target',
      'text' => __('meeting.target.actions.deadline'),
    ];
  }
@endphp
                        <tr>
                          <td>
                            {{ $charge->name }}<br/>{{ $charge->has_amount ?
                              $locale->formatMoney($charge->amount) : __('tontine.labels.fees.variable') }}
                          </td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount) }}@endif
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => $menus,
])
                          </td>
                        </tr>
