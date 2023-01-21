                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $charge->paid_bills_count }}/{{ $charge->bills_count }}<br/>
                            {{ $charge->all_paid_bills_count }}/{{ $charge->all_bills_count }}
                          </td>
                          <td class="table-item-menu" data-fee-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
