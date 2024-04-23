@inject('locale', 'Siak\Tontine\Service\LocaleService')
              <div class="section-title mt-0">{{ __('meeting.actions.pools') }} - {{ $pool->title }}</div>
              <div class="table-responsive">
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{{ __('figures.titles.session') }}</th>
                      <th>{{ __('figures.titles.start') }}</th>
                      <th>{{ __('figures.deposit.titles.count') }}</th>
                      <th>{{ __('figures.deposit.titles.amount') }}</th>
                      <th>{{ __('figures.titles.recv') }}</th>
                      <th>{{ __('figures.remitment.titles.count') }}</th>
                      <th>{{ __('figures.remitment.titles.amount') }}</th>
                      <th>{{ __('figures.titles.end') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td><b>{{ $session->title }}</b></td>
@if($session->disabled($pool) || ($session->pending && !$pool->remit_planned))
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@elseif (!$pool->remit_planned)
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b>
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b>
                      </td>
@elseif($session->pending)
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>-</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}
                      </div></td>
@else
                      <td class="currency"><div style="display:block;">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}
                      </div></td>
                      <td class="currency"><div style="display:block;">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}
                      </div></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
