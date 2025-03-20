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
                      <th style="text-align:right;">{{ __('figures.titles.start') }}</th>
                      <th style="text-align:right;" colspan="2">{{ __('figures.deposit.titles.amount') }}</th>
                      <th style="text-align:right;">{{ __('figures.titles.recv') }}</th>
                      <th style="text-align:right;" colspan="2">{{ __('figures.remitment.titles.amount') }}</th>
                      <th style="text-align:right;">{{ __('figures.titles.end') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if ($poolService->active($pool, $session))
                    <tr>
                      <td>{{ $session->title }}</td>
@if (!$pool->remit_planned)
                      <td class="report-round-pool-amount"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b></td>
                      <td class="report-round-pool-count"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="report-round-pool-amount"><b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b></td>
                      <td class="report-round-pool-amount"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b></td>
                      <td class="report-round-pool-count"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="report-round-pool-amount"><b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b></td>
                      <td class="report-round-pool-amount"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b></td>
@elseif($session->pending)
                      <td class="report-round-pool-amount"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}</td>
                      <td class="report-round-pool-count"><b>-</b><br/>{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="report-round-pool-amount"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}</td>
                      <td class="report-round-pool-amount"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}</td>
                      <td class="report-round-pool-count"><b>-</b><br/>{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="report-round-pool-amount"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}</td>
                      <td class="report-round-pool-amount"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}</td>
@else
                      <td class="report-round-pool-amount">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}
                      </td>
                      <td class="report-round-pool-count">
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }}
                      </td>
                      <td class="report-round-pool-amount">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}
                      </td>
                      <td class="report-round-pool-amount">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}
                      </td>
                      <td class="report-round-pool-count">
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }}
                      </td>
                      <td class="report-round-pool-amount">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}
                      </td>
                      <td class="report-round-pool-amount">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}
                      </td>
@endif
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
              </div>
