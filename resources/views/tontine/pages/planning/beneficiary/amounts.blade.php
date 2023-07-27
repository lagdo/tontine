@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.deposits') }} - {{ $pool->title }}</h2>
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
                <div class="btn-group float-right" role="group" aria-label="">
                  <button type="button" class="btn btn-primary" id="btn-subscription-refresh"><i class="fa fa-sync"></i></button>
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
                      <th>
                        <a href="javascript:void(0)" class="pool-session-toggle" data-session-id="{{ $session->id }}">
                          @if($session->disabled($pool))<i class="fa fa-toggle-off"></i>@else<i class="fa fa-toggle-on"></i>@endif
                        </a>
                        {{ $session->title }}
                      </th>
@if($session->disabled($pool))
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@else
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, true) }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, true) }}</td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, true) }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, true) }}</td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, true) }}</td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
