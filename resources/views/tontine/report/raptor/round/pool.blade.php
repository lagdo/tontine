@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('poolService', 'Siak\Tontine\Service\Planning\PoolService')
              <div class="table-title">
                {{ __('meeting.actions.pools') }} - {{ $pool->title }}
              </div>
              <div class="table">
                <table>
                  <thead>
                    <tr>
                      <th></th>
                      <th style="text-align:right;">{!! __('figures.titles.start') !!}</th>
                      <th style="text-align:right;" colspan="2">{!! __('figures.titles.deposits') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.recv') !!}</th>
                      <th style="text-align:right;" colspan="2">{!! __('figures.titles.remitments') !!}</th>
                      <th style="text-align:right;"@if($pool->remit_auction) colspan="2"@endif>{!! __('figures.titles.auctions') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if ($poolService->active($pool, $session))
@php
  $options = [
    'locale' => $locale,
    'pool' => $pool,
    'session' => $session,
    'expected' => $figures->expected[$session->id] ?? null,
    'collected' => $figures->collected[$session->id],
    'auction' => $figures->auctions[$session->id] ?? null,
  ];
@endphp
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.start', $options)
                      </td>
                      <td class="report-round-pool-count">
                        @include('tontine.report.pool.deposit.count', $options)
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.deposit.amount', $options)
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.recv', $options)
                      </td>
                      <td class="report-round-pool-count">
                        @include('tontine.report.pool.remitment.count', $options)
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.remitment.amount', $options)
                      </td>
@if($pool->remit_auction)
                      <td class="report-round-pool-count">
                        @include('tontine.report.pool.auction.count', $options)
                      </td>
@endif
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.auction.amount', $options)
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.end', $options)
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
              </div>
