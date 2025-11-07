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
  ], [
    'class' => 'btn-fee-libre-savings',
    'text' => __('meeting.settlement.actions.savings'),
  ]];
  if($charge->is_fee && !$charge->has_amount)
  {
    $menus[] = [
      'class' => 'btn-fee-libre-target',
      'text' => __('meeting.target.actions.deadline'),
    ];
  }
  $chargeAmount = $charge->has_amount ? $locale->formatMoney($charge->amount) :
    __('tontine.labels.fees.variable');
@endphp
                        <tr>
                          <td>
                            <div>{{ $charge->name }}</div>
                            <div>
                              <div style="float:left">{{ $chargeAmount }}</div>
@if ($roundBillTotal > 0)
                              <div style="float:right">{{ $roundSettlementTotal }}/{{ $roundBillTotal }}</div>
@endif
                            </div>
                          </td>
                          <td class="currency">
                            <div>{{ $sessionSettlementTotal }}/{{ $sessionBillTotal }}</div>
@if ($sessionSettlementTotal > 0)
                            <div>{{ $locale->formatMoney($sessionSettlementAmount) }}</div>
@endif
                          </td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => $menus,
])
                          </td>
                        </tr>
