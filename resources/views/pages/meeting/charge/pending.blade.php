                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>[{{ $charge->members_paid }}/{{ $charge->members_count }}]</td>
                          <td>&nbsp;</td>
                        </tr>
