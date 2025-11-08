                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{!! $fund->title !!} :: {{ $locale->formatMoney($distribution->profitAmount, true)
                        }}, {{ __('meeting.profit.distribution.parts', [
                          'parts' => $distribution->transfers->sum('parts'),
                        ]) }}</h5>
                    </div>
                  </div>
@if ($distribution->rewarded->count() > 1)
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h6>{!! __('meeting.profit.distribution.basis', [
                        'unit' => $locale->formatMoney($distribution->partAmount, true),
                      ]) !!}</h6>
                    </div>
                  </div>
@endif
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.operation') !!}</th>
                          <th style="text-align:right;">{!! __('common.labels.amount') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.distribution') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@php
  $profitSum = $distribution->transfers->sum('profit');
  $profitAmount = $distribution->profitAmount;
@endphp
@foreach ($distribution->transfers->groupBy('member_id') as $transfers)
@php
  $memberProfit = $transfers->sum('profit');
  $memberPercent = $memberProfit / $profitSum;
@endphp
                        <tr>
                          <td rowspan="{{ $transfers->count() + 1 }}">
                            <b>{{ $transfers[0]->member->name }}</b>
                          </td>
                          <td class="report-savings-session">&nbsp;</td>
                          <td class="report-savings-amount">
                            <b>{{ $locale->formatMoney($transfers->sum('amount'), true) }}</b>
                          </td>
                          <td class="report-savings-amount">
                            <b>{{ $transfers->sum('parts') }} ({{ sprintf('%.2f', $memberPercent * 100) }}%)</b>
                          </td>
                          <td class="report-savings-amount">
                            <b>{{ $locale->formatMoney((int)($profitAmount * $memberPercent), true) }}</b>
                          </td>
                        </tr>
@foreach ($transfers as $transfer)
                        <tr>
                          <td class="report-savings-session">
                            <div>{{ __($transfer->coef > 0 ? 'meeting.labels.saving' : 'meeting.labels.settlement') }}</div>
                            <div>{{ $transfer->session->title }}</div>
                          </td>
                          <td class="report-savings-amount">
                            <div>{{ $locale->formatMoney($transfer->amount * $transfer->coef, true) }}</div>
                            <div>{{ $transfer->duration }}</div>
                          </td>
                          <td class="report-savings-amount">
                            <div>{{ $transfer->amount / $distribution->partAmount }}*{{ $transfer->duration }}</div>
                            <div>={{ $transfer->parts }}</div>
                          </td>
                          <td>&nbsp;</td>
                        </tr>
@endforeach
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
