@inject('locale', 'Siak\Tontine\Service\LocaleService')
              <div class="row mt-0">
                <div class="col d-flex justify-content-center">
                  <h5>{{ __('meeting.actions.pools') }} - {{ $pool->title }} ({{ $currency }})</h5>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered">
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
                    <tr>
                      <td>{{ $session->title }}</td>
@if($session->disabled($pool) || ($session->pending && !$pool->remit_planned))
                      <td class="report-round-pool-amount"></td>
                      <td class="report-round-pool-count"></td>
                      <td class="report-round-pool-amount"></td>
                      <td class="report-round-pool-amount"></td>
                      <td class="report-round-pool-count"></td>
                      <td class="report-round-pool-amount"></td>
                      <td class="report-round-pool-amount"></td>
@elseif (!$pool->remit_planned)
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
@endforeach
                  </tbody>
                </table>
              </div>
