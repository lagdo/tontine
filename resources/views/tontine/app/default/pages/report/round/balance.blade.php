@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $cash = 0;
@endphp
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{{ __('figures.titles.session') }}</th>
                      <th class="currency">{!! __('meeting.titles.fees') !!}</th>
                      <th class="currency">{!! __('meeting.titles.loans') !!}</th>
                      <th class="currency">{!! __('meeting.titles.refunds') !!}</th>
                      <th class="currency">{!! __('meeting.titles.savings') !!}</th>
                      <th class="currency">{!! __('meeting.titles.disbursements') !!}</th>
                      <th class="currency">{!! __('figures.titles.end') !!}</th>
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
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $disbursement = $disbursements[$session->id] ?? 0;
  $balance = $settlement + $refund + $saving - $loan - $disbursement;
  $cash += $balance;
@endphp
                      <td class="currency"><b>{!! $locale->formatMoney($settlement, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($loan, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($refund, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($saving, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($disbursement, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($balance, false, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
