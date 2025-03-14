@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $cash = 0;
@endphp
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{{ __('figures.titles.session') }}</th>
                      <th>{!! __('meeting.titles.auctions') !!}</th>
                      <th>{!! __('meeting.titles.fees') !!}</th>
                      <th>{!! __('meeting.titles.loans') !!}</th>
                      <th>{!! __('meeting.titles.refunds') !!}</th>
                      <th>{!! __('meeting.titles.savings') !!}</th>
                      <th>{!! __('meeting.titles.disbursements') !!}</th>
                      <th>{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td><b>{{ $session->title }}</b></td>
@if($session->pending)
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $auction = $auctions[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $disbursement = $disbursements[$session->id] ?? 0;
  $balance = $auction + $settlement + $refund + $saving - $loan - $disbursement;
  $cash += $balance;
@endphp
                      <td class="currency"><b>{!! $locale->formatMoney($auction, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($settlement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($loan, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($refund, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($saving, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($disbursement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($balance, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
