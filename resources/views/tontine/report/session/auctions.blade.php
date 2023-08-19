@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.remitment.titles.auctions') }}</div>
                    </div>
                  </div>
@foreach ($pools as $pool)
@php
  $total = 0;
@endphp
@if ($session->enabled($pool))
                  <div class="row">
                    <div class="col">
                      <h6>{{ $pool->title }}</h6>
                    </div>
                    <div class="col">
                      <h6>{{ __('common.labels.amount') }}: {{ $locale->formatMoney($pool->paid_amount, true) }}</h6>
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
@foreach ($loans as $loan)
@php
  $payable = $loan->remitment->payable;
@endphp
@if ($payable->subscription->pool_id === $pool->id)
@php
  $total += $loan->interest_debt->amount;
@endphp
                        <tr>
                          <td>{{ $loan->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->interest_debt->amount, true) }}</td>
                          <td class="currency">{{ $loan->interest_debt->refund ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ $locale->formatMoney($total, true) }}</th>
                          <th class="currency">&nbsp;</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
