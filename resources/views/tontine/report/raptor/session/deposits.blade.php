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
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
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
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $pool->paid_count }}@if ($pool->late_count > 0)+{{
                            $pool->late_count }}@endif/{{ $pool->total_count }}</td>
                          <td>{{ $locale->formatMoney($pool->paid_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
