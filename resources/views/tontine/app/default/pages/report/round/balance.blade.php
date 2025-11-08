@php
  $cash = 0;
@endphp
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{{ __('figures.titles.session') }}</th>
                      <th class="currency">{!! __('meeting.titles.fees') !!}</th>
                      <th class="currency">{!! __('meeting.titles.savings') !!}</th>
                      <th class="currency">{!! __('meeting.titles.loans') !!}</th>
                      <th class="currency">{!! __('figures.titles.refunds') !!}</th>
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
  $savingStartAmount = $funds
    ->filter(fn($fund) => $fund->start_sid === $session->id)
    ->sum('start_amount');
  $savingEndAmount = $funds
    ->filter(fn($fund) => $fund->end_sid === $session->id)
    ->sum('end_amount');

  $settlement = $settlements[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $transfer = $transfers[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $outflow = $outflows[$session->id] ?? 0;
  $pool = $pools[$session->id] ?? 0;
  $balance = $settlement + $refund + $saving - $transfer - $loan - $outflow;
  $cash += $balance - $savingEndAmount + $savingStartAmount;
@endphp
                      <td class="currency">
                        <div><b>{!! $locale->formatMoney($settlement - $transfer, false, false) !!}</b></div>
@if ($transfer > 0)
                        <div><b>{!! $locale->formatMoney($transfer, false, false) !!}</b></div>
@endif
                      </td>
                      <td class="currency">
                        <div><b>{!! $locale->formatMoney($saving, false, false) !!}</b></div>
@if ($transfer > 0)
                        <div><b>-&nbsp;{!! $locale->formatMoney($transfer, false, false) !!}</b></div>
@endif
@if ($savingStartAmount > 0)
                        <div><b>+&nbsp;{!! $locale->formatMoney($savingStartAmount, false, false) !!}</b></div>
@endif
@if ($savingEndAmount > 0)
                        <div><b>-&nbsp;{!! $locale->formatMoney($savingEndAmount, false, false) !!}</b></div>
@endif
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($loan, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($refund, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($outflow, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <div><b>{!! $locale->formatMoney($balance, false, false) !!}</b></div>
                        <div><b>{!! $locale->formatMoney($cash, false, false) !!}</b></div>
                      </td>
                      <td class="currency">
                        <div><b>{!! $locale->formatMoney($pool, false, false) !!}</b></div>
                        <div><b>{!! $locale->formatMoney($cash + $pool, false, false) !!}</b></div>
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
