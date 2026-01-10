                        <tr>
                          <td>
                            <div>{{ $charge->name }}</div>
                            <div>{{ $locale->formatMoney($charge->amount) }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $charge->currentBillCount }}</div>
                            <div>{{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $charge->previousBillCount }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($settlements['amount']['current'][$charge->id] ?? 0, false) }}</div>
                            <div>{{ $locale->formatMoney($settlements['amount']['previous'][$charge->id] ?? 0, false) }}</div>
                          </td>
                        </tr>
