                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $locale->formatMoney($charge->amount) }}</td>
                          <td class="currency">
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $charge->currentBillCount }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $charge->previousBillCount }}
                          </td>
                          <td>&nbsp;</td>
                        </tr>
