                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->money('amount') }}</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td class="table-item-menu" data-pool-id="{{ $pool->id }}">
                            <button type="button" class="btn btn-primary {{ $menuClass }}"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
