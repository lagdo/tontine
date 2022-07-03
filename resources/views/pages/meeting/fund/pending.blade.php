                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $fund->recv_paid }}/{{ $fund->recv_count }}<br/>{{ $fund->pay_paid }}/{{ $fund->pay_count }}</td>
                          <td class="table-item-menu"></td>
                        </tr>
