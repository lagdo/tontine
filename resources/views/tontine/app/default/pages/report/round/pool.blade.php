@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('poolService', 'Siak\Tontine\Service\Planning\PoolService')
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('figures.titles.session') !!}</th>
                      <th class="currency">{!! __('figures.titles.start') !!}</th>
                      <th class="currency">{!! __('figures.titles.deposits') !!}</th>
                      <th class="currency">{!! __('figures.titles.recv') !!}</th>
                      <th class="currency">{!! __('figures.titles.remitments') !!}</th>
                      <th class="currency">{!! __('figures.titles.auctions') !!}</th>
                      <th class="currency">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if($poolService->active($pool, $session))
@php
  $expected = $figures->expected[$session->id] ?? null;
  $collected = $figures->collected[$session->id];
  $auction = $figures->auctions[$session->id] ?? null;
@endphp
                    <tr>
                      <td><b>{{ $session->title }}</b></td>
                      <td class="currency">
                        @include('tontine.report.pool.start',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.deposit',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.recv',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.remitment',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.auction',
                          compact('locale', 'pool', 'session', 'auction', 'collected'))
                      </td>
                      <td class="currency">
                        @include('tontine.report.pool.end',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
