                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->paid_bills_count }}/{{ $charge->bills_count }}<br/>
                            {{ $charge->all_paid_bills_count }}/{{ $charge->all_bills_count }}
                          </td>
                          <td></td>
                        </tr>
