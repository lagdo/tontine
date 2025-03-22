@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('poolService', 'Siak\Tontine\Service\Planning\PoolService')
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('figures.titles.session') !!}</th>
                      <th class="currency">{!! __('figures.titles.start') !!}</th>
                      <th class="currency" colspan="2">{!! __('figures.titles.deposits') !!}</th>
                      <th class="currency">{!! __('figures.titles.recv') !!}</th>
                      <th class="currency" colspan="2">{!! __('figures.titles.remitments') !!}</th>
                      <th class="currency">{!! __('figures.titles.auctions') !!}</th>
                      <th class="currency">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if($poolService->active($pool, $session))
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
                      <td class="currency">
                        @include('tontine.report.pool.auction', $options)
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.end', $options)
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
