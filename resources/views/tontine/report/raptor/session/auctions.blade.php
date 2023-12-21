@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {{ __('meeting.remitment.titles.auctions') }}
                  </div>
@foreach ($pools as $pool)
@php
  $total = 0;
@endphp
@if ($pool->remit_auction && $session->enabled($pool))
                  <div class="table-title">
                    {{ $pool->title }} :: {{ $locale->formatMoney($pool->paid_amount, true) }}
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
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td colspan="2">{{ $locale->formatMoney($total, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach
