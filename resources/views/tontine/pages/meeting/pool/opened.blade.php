                        <tr>
                          <td>{{ $pool->title }}@if(!$tontine->is_libre)<br/>{{ $pool->money('amount') }}@endif</td>
                          <td>{{ $paid }}/{{ $count }}</td>
                          <td class="table-item-menu" data-pool-id="{{ $pool->id }}">
                            <button type="button" class="btn btn-primary {{ $menuClass }}"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
