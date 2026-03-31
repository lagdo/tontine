                  <div class="section-title">
                    {{ __('meeting.titles.remitments') }}
                  </div>
@foreach ($pools as $pool)
@if ($pool->sessions->pluck('id', 'id')->has($session->id))
@php
  $poolPayables = $payables->filter(fn($payable) => $payable->pool->id === $pool->id);
@endphp
                  <div class="table-title">
                    {{ $pool->title }} :: {{ $pool->deposit_fixed ?
                      $locale->formatMoney($pool->amount, true) : ('(' . __('tontine.labels.types.libre') . ')') }}
                  </div>
                  <div class="table">
                    <table>
@if ($poolPayables->count() > 0)
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
@endif
                      <tbody>
@foreach ($poolPayables as $payable)
                        <tr>
                          <td>{{ $payable->member->name }}</td>
                          <td style="text-align:right;">{{ $payable->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                          <td style="text-align:right;">{{ $payable->paid ? $locale->formatMoney($payable->amount, true) : '-' }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td colspan="2">{{ $locale->formatMoney($pool->paid_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
