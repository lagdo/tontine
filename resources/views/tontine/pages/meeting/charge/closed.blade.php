                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $charge->currentBillCount }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $charge->previousBillCount }}
                          </td>
                          <td>
                            {{ $settlements['amount']['current'][$charge->id] ?? $zero }}<br/>
                            {{ $settlements['amount']['previous'][$charge->id] ?? $zero }}
                          </td>
                        </tr>
