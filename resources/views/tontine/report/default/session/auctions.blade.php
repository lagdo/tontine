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
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
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
                          <td style="text-align:right;">{{ $auction->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($auction->amount, true) }}</td>
                        </tr>
@endif
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th style="text-align:right;" colspan="2">{{ $locale->formatMoney($total, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
