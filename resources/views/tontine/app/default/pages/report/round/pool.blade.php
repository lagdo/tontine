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
                    <tr>
                      <td><b>{{ $session->title }}</b></td>
@if (!$pool->remit_planned)
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b> /
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b> /
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false, false) !!}</b>
                      </td>
@elseif($session->pending)
                      <td class="currency"><div>
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>-</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }} /
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>-</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }} /
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false, false) }}
                      </div></td>
@else
                      <td class="currency"><div>
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b> /
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false, false) !!}</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }} /
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false, false) }}
                      </div></td>
                      <td class="currency"><div>
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b> /
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false, false) !!}</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }} /
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false, false) }}
                      </div></td>
@endif
<!-- Auction cell start -->
@php
  $auctionAmount = $figures->auctions[$session->id]?->amount ?? 0;
  $cashierEnd = $figures->collected[$session->id]->cashier->end;
@endphp
                      <td class="currency"><div>
@if(!$session->pending && $pool->remit_auction)
                        <b>{!! $figures->auctions[$session->id]?->count ?? 0 !!}</b> /
                        <b>{!! $locale->formatMoney($auctionAmount, false, false) !!}</b>
@else
                        <b>-</b>
@endif
@if ($pool->remit_planned)
                        <br/><b>{!! $locale->formatMoney($cashierEnd - $auctionAmount, false, false) !!}</b>
@endif
                      </div></td>
<!-- Auction cell end -->
@if (!$pool->remit_planned)
                      <td class="currency">
                        <b>{!! $locale->formatMoney($cashierEnd, false, false) !!}</b>
                      </td>
@elseif($session->pending)
                      <td class="currency"><div>
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false, false) }}
                      </div></td>
@else
                      <td class="currency"><div>
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false, false) }}
                      </div></td>
@endif
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
