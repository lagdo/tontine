                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.remitments') }}</h5>
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
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
@if ($payable->pool->id === $pool->id)
                        <tr>
                          <td>{{ $payable->member->name }}</td>
                          <td style="text-align:right;">{{ $payable->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                          <td style="text-align:right;">{{ $payable->paid ? $locale->formatMoney($payable->amount, true) : '-' }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th style="text-align:right;" colspan="2">{{ $locale->formatMoney($pool->paid_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
