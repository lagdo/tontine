@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $currBillTotal = $bills['total']['current'][$charge->id] ?? 0;
  $prevBillTotal = $bills['total']['previous'][$charge->id] ?? 0;
  $currSettlementTotal = $settlements['total']['current'][$charge->id] ?? 0;
  $prevSettlementTotal = $settlements['total']['previous'][$charge->id] ?? 0;
  $currSettlementAmount = $settlements['amount']['current'][$charge->id] ?? 0;
  $menus = [[
    'class' => 'btn-fee-libre-add',
    'text' => __('common.actions.add'),
  ],[
    'class' => 'btn-fee-libre-settlements',
    'text' => __('meeting.actions.settlements'),
  ]];
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
                            {{ $charge->name }}<br/>
                            {{ $charge->has_amount ? $locale->formatMoney($charge->amount, true) :
                              __('tontine.labels.fees.variable') }}
                          </td>
                          <td class="currency">
                            {{ $currSettlementTotal }}/{{ $currBillTotal }} @if ($prevBillTotal > 0) - {{
                              $prevSettlementTotal }}/{{ $prevBillTotal }}@endif @if ($currSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($currSettlementAmount, true) }}@endif
                          </td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-charge-id',
  'dataIdValue' => $charge->id,
  'menus' => $menus,
])
                          </td>
                        </tr>
