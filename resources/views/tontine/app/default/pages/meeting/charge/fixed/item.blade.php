@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $currBillTotal = $bills['total']['current'][$charge->id] ?? 0;
  $prevBillTotal = $bills['total']['previous'][$charge->id] ?? 0;
  $currSettlementTotal = $settlements['total']['current'][$charge->id] ?? 0;
  $prevSettlementTotal = $settlements['total']['previous'][$charge->id] ?? 0;
  $currSettlementAmount = $settlements['amount']['current'][$charge->id] ?? 0;
  if(!$charge->period_session)
  {
    $currBillTotal -= $prevSettlementTotal; // Remove the bills that are already settled.
  }
@endphp
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $locale->formatMoney($charge->amount, true) }}</td>
                          <td class="currency">
                            {{ $currSettlementTotal }}/{{ $currBillTotal }} @if ($prevBillTotal > 0) - {{
                              $prevSettlementTotal }}/{{ $prevBillTotal }}@endif @if ($currSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($currSettlementAmount, true) }}@endif
                          </td>
                          <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-fixed-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
