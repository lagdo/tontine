                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->money('amount') }}</td>
                          <td>
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $bills['total']['current'][$charge->id] ?? 0 }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $bills['total']['previous'][$charge->id] ?? 0 }}
                          </td>
                          <td class="table-item-menu" data-fee-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
