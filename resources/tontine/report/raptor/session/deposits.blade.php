                  <div class="section-title">
                    {{ __('meeting.titles.deposits') }}
                  </div>
@foreach ($pools as $pool)
@if ($pool->sessions->pluck('id', 'id')->has($session->id))
                  <div class="table-title">
                    {{ $pool->title }} :: {{ $pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount, true) : ('(' . __('tontine.labels.types.libre') . ')') }}
                  </div>
                  <div class="table">
                    <table>
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
                        <tr class="total">
                          <td>
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
                          </td>
                          <td>
                            {{ $pool->paid_count }}/{{ $pool->recv_count }}
                          </td>
                          <td>{{ $locale->formatMoney($pool->recv_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->

{{-- List the extra deposits --}}
@php
  $extraDeposits = $extras->filter(fn($deposit) => $deposit->pool->id === $pool->id);
@endphp
@if (count($extraDeposits) > 0)
                  <div class="table">
                    <table>
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
                        <tr class="total">
                          <td>
@if ($pool->next_early > 0)
                            {{ __('meeting.deposit.counts.early', ['count' => $pool->next_early]) }}
@endif
@if ($pool->prev_late > 0)
                            {{ __('meeting.deposit.counts.late', ['count' => $pool->prev_late]) }}
@endif
                            {{ __('common.labels.total') }}
                          </td>
                          <td>
                            {{ $pool->next_early + $pool->prev_late }}
                          </td>
                          <td>{{ $locale->formatMoney($pool->extra_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif

@endif
@endforeach
