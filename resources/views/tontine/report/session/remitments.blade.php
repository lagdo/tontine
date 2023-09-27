@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }}</div>
                    </div>
                  </div>
@foreach ($pools as $pool)
@if ($session->enabled($pool))
                  <div class="row">
                    <div class="col">
                      <h6>{{ $pool->title }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ $pool->title }}<br/>{{ $pool->deposit_fixed ?
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
@foreach ($payables as $payable)
@if ($payable->pool->id === $pool->id)
                        <tr>
                          <td>{{ $payable->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($payable->amount, true) }}</td>
                          <td class="currency">{{ $payable->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ $locale->formatMoney($pool->paid_amount, true) }}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
