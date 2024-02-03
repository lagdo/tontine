@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
  $menus = [];
  if($charge->is_active)
  {
    $menus[] = [
      'class' => 'btn-fee-libre-add',
      'text' => __('common.actions.add'),
    ];
  }
  $menus[] = [
    'class' => 'btn-fee-libre-settlements',
    'text' => __('meeting.actions.settlements'),
  ];
  if($charge->is_fee)
  {
    $menus[] = [
      'class' => 'btn-fee-libre-target',
      'text' => __('meeting.target.actions.deadline'),
    ];
  }
@endphp
                        <tr>
                          <td>
                            <span @if (!$charge->is_active)style="text-decoration:line-through"@endif>{{ $charge->name }}</span>
                            <br/>{{ $charge->has_amount ? $locale->formatMoney($charge->amount, true) :
                              __('tontine.labels.fees.variable') }}
                          </td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount, true) }}@endif
                          </td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => $menus,
])
                          </td>
                        </tr>
