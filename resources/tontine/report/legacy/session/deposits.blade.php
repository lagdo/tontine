                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.deposits') }}</h5>
                    </div>
                  </div>
@foreach ($pools as $pool)
@if ($pool->sessions->pluck('id', 'id')->has($session->id))
                  <div class="row">
                    <div class="col">
                      <h6>{{ $pool->title }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ __('common.labels.amount') }}: {{ $pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount, true) : __('tontine.labels.types.libre') }}</h6>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
@if ($receivables->count() > 0)
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
@endif
                      <tbody>
@foreach ($receivables as $receivable)
@if ($receivable->pool->id === $pool->id)
                        <tr>
                          <td>{{ $receivable->member->name }}</td>
                          <td style="text-align:right;">{{ !$receivable->paid ?
                            __('common.labels.no') : (!$receivable->paid_late ?
                              __('common.labels.yes') : __('meeting.deposit.labels.late')) }}</td>
                          <td style="text-align:right;">{{ !$receivable->paid ? '-' :
                            $locale->formatMoney($receivable->amount, true) }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th style="text-align:left;">
@if ($pool->paid_early > 0 || $pool->paid_late > 0)
                            {{ __('meeting.deposit.counts.ontime', ['count' => $pool->paid_here]) }}
@if ($pool->paid_early > 0)
                            {{ __('meeting.deposit.counts.early', ['count' => $pool->paid_early]) }}
@endif
@if ($pool->paid_late > 0)
                            {{ __('meeting.deposit.counts.late', ['count' => $pool->paid_late]) }}
@endif
@endif
                            {{ __('common.labels.total') }}
                          </th>
                          <th style="text-align:right;">
                            {{ $pool->paid_count }}/{{ $pool->recv_count }}
                          </th>
                          <th style="text-align:right;">{{ $locale->formatMoney($pool->recv_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->

{{-- List the extra deposits --}}
@php
  $extraDeposits = $extras->filter(fn($deposit) => $deposit->pool->id === $pool->id);
@endphp
@if (count($extraDeposits) > 0)
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:25%;text-align:left;">{{ __('meeting.labels.session') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($extraDeposits as $deposit)
                        <tr>
                          <td>{{ $deposit->member->name }}</td>
                          <td style="text-align:left;">{{ $deposit->receivable->session->title }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($deposit->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <th>
@if ($pool->next_early > 0)
                            {{ __('meeting.deposit.counts.early', ['count' => $pool->next_early]) }}
@endif
@if ($pool->prev_late > 0)
                            {{ __('meeting.deposit.counts.late', ['count' => $pool->prev_late]) }}
@endif
                            {{ __('common.labels.total') }}
                          </th>
                          <th style="text-align:right;">
                            {{ $pool->next_early + $pool->prev_late }}
                          </th>
                          <th style="text-align:right;">
                            {{ $locale->formatMoney($pool->extra_amount, true) }}
                          </th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif

@endif
@endforeach
