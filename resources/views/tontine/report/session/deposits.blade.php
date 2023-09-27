@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="pagebreak"></div>

                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
                    </div>
                  </div>
@foreach ($pools as $pool)
@if ($session->enabled($pool))
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
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="currency">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($receivables as $receivable)
@if ($receivable->pool->id === $pool->id)
                        <tr>
                          <td>{{ $receivable->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($receivable->amount, true) }}</td>
                          <td class="currency">{{ $receivable->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ $locale->formatMoney($pool->paid_amount, true) }}</th>
                          <th class="currency">{{ $pool->paid_count }}/{{ $pool->total_count }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
