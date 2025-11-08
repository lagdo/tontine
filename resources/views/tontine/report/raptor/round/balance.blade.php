@php
  $cash = 0;
@endphp
              <div class="table-title">
                {{ __('meeting.titles.amounts') }}
              </div>
              <div class="table">
                <table>
                  <thead>
                    <tr>
                      <th></th>
                      <th class="report-round-cash-amount">{!! __('meeting.titles.fees') !!}</th>
                      <th class="report-round-cash-amount">{!! __('meeting.titles.savings') !!}</th>
                      <th class="report-round-cash-amount">{!! __('meeting.titles.loans') !!}</th>
                      <th class="report-round-cash-amount">{!! __('figures.titles.refunds') !!}</th>
                      <th class="report-round-cash-amount">{!! __('figures.titles.outflows') !!}</th>
                      <th class="report-round-cash-amount">{!! __('figures.titles.subtotals') !!}</th>
                      <th class="report-round-cash-amount">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}</td>
@if($session->pending)
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
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
                      <td class="report-round-cash-amount">
                        <div><b>{!! $locale->formatMoney($settlement - $transfer, false) !!}</b></div>
@if ($transfer > 0)
                        <div><b>{!! $locale->formatMoney($transfer, false) !!}</b></div>
@endif
                      </td>
                      <td class="report-round-cash-amount">
                        <div><b>{!! $locale->formatMoney($saving, false) !!}</b></div>
@if ($transfer > 0)
                        <div><b>-&nbsp;{!! $locale->formatMoney($transfer, false) !!}</b></div>
@endif
@if ($savingStartAmount > 0)
                        <div><b>+&nbsp;{!! $locale->formatMoney($savingStartAmount, false) !!}</b></div>
@endif
@if ($savingEndAmount > 0)
                        <div><b>-&nbsp;{!! $locale->formatMoney($savingEndAmount, false) !!}</b></div>
@endif
                      </td>
                      <td class="report-round-cash-amount">
                        <b>{!! $locale->formatMoney($loan, false) !!}</b>
                      </td>
                      <td class="report-round-cash-amount">
                        <b>{!! $locale->formatMoney($refund, false) !!}</b>
                      </td>
                      <td class="report-round-cash-amount">
                        <b>{!! $locale->formatMoney($outflow, false) !!}</b>
                      </td>
                      <td class="report-round-cash-amount">
                        <div><b>{!! $locale->formatMoney($balance, false) !!}</b></div>
                        <div><b>{!! $locale->formatMoney($cash, false) !!}</b></div>
                      </td>
                      <td class="report-round-cash-amount">
                        <div><b>{!! $locale->formatMoney($pool, false) !!}</b></div>
                        <div><b>{!! $locale->formatMoney($cash + $pool, false) !!}</b></div>
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
