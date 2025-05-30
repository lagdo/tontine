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
                      <th class="report-round-cash-amount">{!! __('meeting.titles.loans') !!}</th>
                      <th class="report-round-cash-amount">{!! __('figures.titles.refunds') !!}</th>
                      <th class="report-round-cash-amount">{!! __('meeting.titles.savings') !!}</th>
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
  $settlement = $settlements[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $outflow = $outflows[$session->id] ?? 0;
  $pool = $pools[$session->id] ?? 0;
  $balance = $settlement + $refund + $saving - $loan - $outflow;
  $cash += $balance;
@endphp
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($settlement, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($loan, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($refund, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($saving, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($outflow, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($balance, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($pool, false) !!}<br/>{!!
                        $locale->formatMoney($cash + $pool, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
