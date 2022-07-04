                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $fund->recv_paid }}/{{ $fund->recv_count }}<br/>{{ $fund->pay_paid }}/{{ $fund->pay_count }}</td>
                          <td>{{ $summary['receivables'][$fund->id] ?? 0 }}<br/>{{ $summary['payables'][$fund->id] ?? 0 }}</td>
                        </tr>
