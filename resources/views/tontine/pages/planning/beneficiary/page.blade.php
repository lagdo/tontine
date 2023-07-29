@inject('locale', 'Siak\Tontine\Service\LocaleService')
          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool.titles.remitments') }} - {{ $pool->title }}</h2>
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
                      <th>{{ __('figures.titles.sessions') }}</th>
                      <th class="currency">{{ __('figures.titles.amount') }}</th>
                      <th class="currency">{{ __('figures.remitment.titles.count') }}</th>
                      <th class="currency">{{ __('figures.remitment.titles.amount') }}</th>
                      <th>{{ __('figures.titles.beneficiaries') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
@if ($session->disabled($pool))
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                    </tr>
@else
                    <tr>
                      <td>{{ $session->title }}</td>
@if ($tontine->is_libre)
                      <td class="currency">0</td>
                      <td class="currency">1</td>
                      <td class="currency">{{ __('tontine.labels.types.libre') }}</td>
@else
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, true) }}</td>
                      <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                      <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, true) }}</td>
@endif
                      <td>
@foreach ($session->beneficiaries as $subscription)
                        {!! Form::select('', $subscription === 0 ? $subscriptions :
                          collect($subscriptions->all())->put($subscription, $beneficiaries[$subscription] ?? 'Not found'), $subscription, [
                            'class' => 'form-control my-2 select-beneficiary',
                            'data-session-id' => $session->id,
                            'data-subscription-id' => $subscription,
                          ]) !!}
@endforeach
                      </td>
                    </tr>
@endif
@endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
