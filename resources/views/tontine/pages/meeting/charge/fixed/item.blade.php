@inject('locale', 'Siak\Tontine\Service\LocaleService')
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $locale->formatMoney($charge->amount, true) }}</td>
                          <td class="currency">
                            {{ $settlements['total']['current'][$charge->id] ?? 0 }}/{{ $charge->currentBillCount }}<br/>
                            {{ $settlements['total']['previous'][$charge->id] ?? 0 }}/{{ $charge->previousBillCount }}
                          </td>
                          <td class="table-item-menu" data-fee-id="{{ $charge->id }}">
                            <button type="button" class="btn btn-primary btn-fee-settlements"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
