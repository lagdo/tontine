@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
@endphp
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $locale->formatMoney($charge->amount, true) }}</td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount, true) }}@endif
                          </td>
                          <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-fixed-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
