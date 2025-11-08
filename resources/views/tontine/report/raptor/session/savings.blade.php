                  <div class="section-title">
                    {!! __('meeting.titles.savings') !!}
                  </div>
                  <div class="table">
                    <table>
@if ($transfers->count() > 0)
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;">{{ __('tontine.fund.labels.fund') }}</th>
                          <th style="width:10%;">&nbsp;</th>
                          <th style="width:20%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
@endif
                      <tbody>
@foreach ($transfers as $transfer)
                        <tr>
                          <td>{{ $transfer->member->name }}</td>
                          <td>{!! $transfer->fund->title !!}</td>
                          <td style="width:10%;">{!! $transfer->type !!}</td>
                          <td style="width:20%;text-align:right;">{{ $locale->formatMoney($transfer->amount, true) }}</td>
                        </tr>
@endforeach
@php
  $savings = $transfers->filter(fn($transfer) => $transfer->coef > 0);
  $settlements = $transfers->filter(fn($transfer) => $transfer->coef < 0);
@endphp
                        <tr class="total">
                          <th>&nbsp;</th>
                          <td>{{ __('meeting.titles.savings') }}</td>
                          <td style="width:30%;">{{ $savings->count() }}</td>
                          <td style="width:20%;">{{ $locale->formatMoney($savings->sum('amount'), true) }}</td>
                        </tr>
                        <tr class="total">
                          <th>&nbsp;</th>
                          <td>{{ __('meeting.titles.settlements') }}</td>
                          <td style="width:30%;">{{ $settlements->count() }}</td>
                          <td style="width:20%;">{{ $locale->formatMoney($settlements->sum('amount'), true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
