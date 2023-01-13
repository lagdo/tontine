                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td>{{ $report[$pool->id] ?? 0 }}</td>
                        </tr>
