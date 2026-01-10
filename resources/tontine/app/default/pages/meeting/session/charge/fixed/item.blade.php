@php
  $sessionBillTotal = $bills['total']['session'][$charge->id] ?? 0;
  $roundBillTotal = $bills['total']['round'][$charge->id] ?? 0;
  $sessionSettlementTotal = $settlements['total']['session'][$charge->id] ?? 0;
  $roundSettlementTotal = $settlements['total']['round'][$charge->id] ?? 0;
  $sessionSettlementAmount = $settlements['amount']['session'][$charge->id] ?? 0;
@endphp
                        <tr>
                          <td>
                            <div>{{ $charge->name }}</div>
                            <div>
                              <div style="float:left">{{ $locale->formatMoney($charge->amount) }}</div>
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
                          <td class="table-item-menu" data-charge-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-fixed-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
