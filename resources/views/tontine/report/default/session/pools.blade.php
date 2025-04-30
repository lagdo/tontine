@php
  $deposits = $pools['deposit'];
  $remitments = $pools['remitment']->keyBy('id');
@endphp
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.summary') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th colspan="2">{{ __('meeting.titles.deposits') }}</th>
                          <th colspan="2">{{ __('meeting.titles.remitments') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($deposits as $pool)
@if ($pool->sessions->pluck('id', 'id')->has($session->id))
@php
  $rpool = $remitments[$pool->id];
@endphp
                        <tr>
                          <td>{{ $pool->title }}</td>
                          <td style="width:10%;text-align:right;">@if ($pool->paid_count > 0){{ $pool->paid_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($pool->paid_count > 0){{
                            $locale->formatMoney($pool->paid_amount, true) }}@else &nbsp; @endif</td>
                          <td style="width:10%;text-align:right;">@if ($rpool !== null && $rpool->paid_count > 0){{
                            $rpool->paid_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($rpool !== null && $rpool->paid_count > 0){{
                            $locale->formatMoney($rpool->paid_amount, true) }}@else &nbsp; @endif</td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
