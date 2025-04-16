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
                      <th class="currency">{!! __('figures.titles.refunds') !!}</th>
                      <th class="currency">{!! __('meeting.titles.savings') !!}</th>
                      <th class="currency">{!! __('figures.titles.outflows') !!}</th>
                      <th class="currency">{!! __('figures.titles.subtotals') !!}</th>
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
                      <td></td>
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $outflow = $outflows[$session->id] ?? 0;
  $pool = $pools[$session->id] ?? 0;
  $balance = $settlement + $refund + $saving - $loan - $outflow;
  $cash += $balance;
@endphp
                      <td class="currency"><b>{!! $locale->formatMoney($settlement, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($loan, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($refund, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($saving, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($outflow, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($balance, false, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($pool, false, false) !!}<br/>{!!
                        $locale->formatMoney($cash + $pool, false, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
