@inject('locale', 'Siak\Tontine\Service\LocaleService')
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th>{!! __('common.labels.amount') !!}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($debts as $debt)
                    <tr>
                      <td>{{ $debt->loan->member->name }}<br/>{{ $debt->loan->session->title }}</td>
                      <td>{{ $locale->formatMoney($debt->amount) }}</td>
                      <td data-debt-id="{{ $debt->id }}">
                        {!! paymentLink($debt->refund, $type . '-refund', $session->closed) !!}
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
