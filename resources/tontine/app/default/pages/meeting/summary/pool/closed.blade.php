                        <tr>
                          <td>
                            <div>{{ $pool->title }}</div>
                            <div>{{ $amount }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $paid }}/{{ $count < 0 ? '-' : $count }}</div>
@if ($total > 0)
                            <div>{{ $locale->formatMoney($total) }}</div>
@endif
                          </td>
                          <td class="table-item-menu" data-pool-id="{{ $pool->id }}">
                            <button type="button" class="btn btn-primary {{ $menuClass }}"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
