@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($bills as $bill)
                        <tr>
                          <td>{{ $bill->member->name }}@if ($bill->session) <br/>{{ $bill->session->title }} @endif</td>
                          <td class="currency">{{ $locale->formatMoney($bill->amount) }}</td>
                          <td data-bill-id="{{ $bill->id }}" class="table-item-menu">
                            {!! paymentLink($bill->settlement, 'settlement', $session->closed) !!}
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
