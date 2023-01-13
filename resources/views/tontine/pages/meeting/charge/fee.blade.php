                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->getCurrSettlementCount($settlements) }}/{{ $charge->getCurrBillCount($bills) }}<br/>
                            {{ $charge->getPrevSettlementCount($settlements) }}/{{ $charge->getPrevBillCount($bills) }}
                          </td>
                          <td class="table-item-menu" data-fee-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
