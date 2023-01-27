                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $bills['total']['current'][$charge->id] ?? 0 }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $bills['total']['previous'][$charge->id] ?? 0 }}
                          </td>
                          <td>&nbsp;</td>
                        </tr>
