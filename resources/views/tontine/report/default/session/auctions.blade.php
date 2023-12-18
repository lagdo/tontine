@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.remitment.titles.auctions') }}</h5>
                    </div>
                  </div>
@foreach ($pools as $pool)
@php
  $total = 0;
@endphp
@if ($pool->remit_auction && $session->enabled($pool))
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
@foreach ($auctions as $auction)
@if ($auction->pool->id === $pool->id)
@php
  $total += $auction->amount;
@endphp
                        <tr>
                          <td>{{ $auction->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($auction->amount, true) }}</td>
                          <td class="currency">{{ $auction->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ $locale->formatMoney($total, true) }}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
