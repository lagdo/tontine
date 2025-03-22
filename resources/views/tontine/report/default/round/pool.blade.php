@inject('locale', 'Siak\Tontine\Service\LocaleService')
@inject('poolService', 'Siak\Tontine\Service\Planning\PoolService')
              <div class="row mt-0">
                <div class="col d-flex justify-content-center">
                  <h5>{{ __('meeting.actions.pools') }} - {{ $pool->title }}</h5>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th></th>
                      <th style="text-align:right;">{!! __('figures.titles.start') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.deposits') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.recv') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.remitments') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.auctions') !!}</th>
                      <th style="text-align:right;">{!! __('figures.titles.end') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if($poolService->active($pool, $session))
@php
  $expected = $figures->expected[$session->id] ?? [];
  $collected = $figures->collected[$session->id];
  $auction = $figures->auctions[$session->id] ?? null;
@endphp
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.start',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="report-round-pool-count">
                        @include('tontine.report.pool.deposit',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.recv',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="report-round-pool-count">
                        @include('tontine.report.pool.remitment',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.auction',
                          compact('locale', 'pool', 'session', 'auction', 'collected'))
                      </td>
                      <td class="report-round-pool-amount">
                        @include('tontine.report.pool.end',
                          compact('locale', 'pool', 'session', 'expected', 'collected'))
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
              </div>
