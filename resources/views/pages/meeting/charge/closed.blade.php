                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>[{{ $charge->members_paid }}/{{ $charge->members_count }}]</td>
                          <td>
                            {{ $summary['settlements'][$charge->id] ?? 0 }}
                          </td>
                        </tr>
