                  <div class="section-title">
                    {{ __('meeting.remitment.titles.auctions') }}
                  </div>
@foreach ($pools as $pool)
@if ($pool->remit_auction && $pool->sessions->pluck('id', 'id')->has($session->id))
@php
  $total = 0;
  $poolAuctions = $auctions->filter(fn($auction) => $auction->pool->id === $pool->id);
@endphp
                  <div class="table-title">
                    {{ $pool->title }} :: {{ $locale->formatMoney($pool->paid_amount, true) }}
                  </div>
                  <div class="table">
                    <table>
@if ($poolAuctions->count() > 0)
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
@endif
                      <tbody>
@foreach ($poolAuctions as $auction)
@php
  $total += $auction->amount;
@endphp
                        <tr>
                          <td>{{ $auction->member->name }}</td>
                          <td style="text-align:right;">{{ $auction->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($auction->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td colspan="2">{{ $locale->formatMoney($total, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
