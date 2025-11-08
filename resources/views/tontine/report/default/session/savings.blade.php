                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{!! __('meeting.titles.savings') !!}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
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
                          <td style="text-align:right;">{{ $locale->formatMoney($transfer->amount, true) }}</td>
                        </tr>
@endforeach
@php
  $savings = $transfers->filter(fn($transfer) => $transfer->coef > 0);
  $settlements = $transfers->filter(fn($transfer) => $transfer->coef < 0);
@endphp
                        <tr>
                          <th>&nbsp;</th>
                          <th>{{ __('meeting.titles.savings') }}</th>
                          <th style="width:30%;text-align:right;">{{ $savings->count() }}</th>
                          <th style="width:20%;text-align:right;">{{ $locale->formatMoney($savings->sum('amount'), true) }}</th>
                        </tr>
                        <tr>
                          <th>&nbsp;</th>
                          <th>{{ __('meeting.titles.settlements') }}</th>
                          <th style="width:30%;text-align:right;">{{ $settlements->count() }}</th>
                          <th style="width:20%;text-align:right;">{{ $locale->formatMoney($settlements->sum('amount'), true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
