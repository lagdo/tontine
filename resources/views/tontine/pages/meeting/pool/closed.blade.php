                        <tr>
                          <td>{{ $pool->title }}@if(!$tontine->is_libre)<br/>{{ $pool->money('amount') }}@endif</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td>{{ $amounts[$pool->id] ?? '' }}</td>
                        </tr>
