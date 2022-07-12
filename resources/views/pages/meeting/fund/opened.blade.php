                        <tr>
                          <td>{{ $fund->title }}<br/>{{ $fund->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td class="table-item-menu" data-fund-id="{{ $fund->id }}">
                            <button type="button" class="btn btn-primary {{ $menuClass }}"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
