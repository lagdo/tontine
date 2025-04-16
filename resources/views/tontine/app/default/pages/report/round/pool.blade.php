@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolSessionIds = $pool->sessions->pluck('id', 'id');
@endphp
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('figures.titles.session') !!}</th>
                      <th class="currency">{!! __('figures.titles.start') !!}</th>
                      <th class="currency" colspan="2">{!! __('figures.titles.deposits') !!}</th>
                      <th class="currency">{!! __('figures.titles.recv') !!}</th>
                      <th class="currency" colspan="2">{!! __('figures.titles.remitments') !!}</th>
                      <th class="currency"@if($pool->remit_auction) colspan="2"@endif>{!! __('figures.titles.auctions') !!}</th>
                      <th class="currency">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if($poolSessionIds->has($session->id))
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
                      <td><b>{{ $session->title }}</b></td>
                      <td class="currency">
                        @include('tontine.report.pool.start', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.deposit.count', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.deposit.amount', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.recv', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.remitment.count', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.remitment.amount', $options)
                      </td>
@if($pool->remit_auction)
                      <td class="currency">
                        @include('tontine.report.pool.auction.count', $options)
                      </td>
@endif
                      <td class="currency">
                        @include('tontine.report.pool.auction.amount', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.end', $options)
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
