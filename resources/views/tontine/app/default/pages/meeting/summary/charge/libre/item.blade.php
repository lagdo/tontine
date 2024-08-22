@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
  @endphp
                        <tr>
                          <td @if (!$charge->is_active) style="text-decoration:line-through" @endif>
                            {{ $charge->name }}<br/>{{ $charge->has_amount ?
                              $locale->formatMoney($charge->amount, true) : __('tontine.labels.fees.variable') }}
                          </td>
                          <td class="currency">
                            {{ $sessionSettlementTotal }}/{{ $sessionBillTotal }} @if ($roundBillTotal > 0) - {{
                              $roundSettlementTotal }}/{{ $roundBillTotal }}@endif @if ($sessionSettlementAmount > 0)<br/>{{
                              $locale->formatMoney($sessionSettlementAmount, true) }}@endif
                          </td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
