                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $amount }}</td>
                          <td class="currency">{{ $paid }}/{{ $count < 0 ? '-' : $count
                            }}@if ($total > 0)<br/>{{ $locale->formatMoney($total) }}@endif</td>
                          <td class="table-item-menu" data-pool-id="{{ $pool->id }}">
                            <button type="button" class="btn btn-primary {{ $menuClass }}"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
