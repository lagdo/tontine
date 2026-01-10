                        <tr>
                          <td>
                            <div>{{ $pool->title }}</div>
                            <div>{{ $amount }}</div>
                          </td>
                          <td class="currency">{{ $paid }}/{{ $count < 0 ? '-' : $count }}</td>
                          <td class="table-item-menu"></td>
                        </tr>
