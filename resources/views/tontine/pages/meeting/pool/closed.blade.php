                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td>{{ $amounts[$pool->id] ?? '' }}</td>
                        </tr>
