                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td>{{ $summary[$fund->id] ?? 0 }}</td>
                        </tr>
