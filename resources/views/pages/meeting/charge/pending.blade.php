                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->getCurrSettlementCount($settlements) }}/{{ $charge->getCurrBillCount($bills) }}<br/>
                            {{ $charge->getPrevSettlementCount($settlements) }}/{{ $charge->getPrevBillCount($bills) }}
                          </td>
                          <td>&nbsp;</td>
                        </tr>
