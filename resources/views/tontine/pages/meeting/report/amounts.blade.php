@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ $pool->title }} - {{ __('figures.titles.amounts') }} ({{ $locale->getCurrencyName() }})</h2>
              </div>
              <div class="col-auto">
                <div class="input-group float-right ml-2">
                  {!! Form::select('pool_id', $pools, $pool->id, ['class' => 'form-control', 'id' => 'select-pool']) !!}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-pool-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-meeting-report-refresh"><i class="fa fa-sync"></i></button>
                  <a type="button" class="btn btn-primary" target="_blank" href="{{ route('report.pool', ['poolId' => $pool->id]) }}"><i class="fa fa-file-pdf"></i></a>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th></th>
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
                      <th>{{ $session->title }}</th>
@if($session->disabled($pool) || ($tontine->is_libre && $session->pending))
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@elseif ($tontine->is_libre)
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->deposit->count !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b></td>
                      <td class="currency"><b>{!! $figures->collected[$session->id]->remitment->count !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b></td>
@elseif($session->pending)
                      <td class="currency"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}</td>
                      <td class="currency"><b>-</b><br/>{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="currency"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}</td>
                      <td class="currency"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}</td>
                      <td class="currency"><b>-</b><br/>{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}</td>
                      <td class="currency"><b>-</b><br/>{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}</td>
@else
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->start, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->deposit->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->deposit->count }}
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->deposit->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->recv, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}
                      </td>
                      <td class="currency">
                        <b>{!! $figures->collected[$session->id]->remitment->count !!}</b><br/>
                        {{ $figures->expected[$session->id]->remitment->count }}
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->remitment->amount, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}
                      </td>
                      <td class="currency">
                        <b>{!! $locale->formatMoney($figures->collected[$session->id]->cashier->end, false) !!}</b><br/>
                        {{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
