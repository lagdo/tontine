@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $rqSubscription = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Subscription::class);
  $rqBeneficiary = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Beneficiary::class);
  $rqPlanning = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Planning::class);
@endphp
            <div class="col-md-12">
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">
                      {{ __('tontine.subscription.titles.planning') }}:
                      {{ $pool->title . ' - ' . ($pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre')) }}
                    </h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqBeneficiary->render())>{{
                        __('tontine.subscription.titles.beneficiaries') }}</i></button>
                    </div>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSubscription->render())><i class="fa fa-arrow-left"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqPlanning->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
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
@if($session->disabled($pool))
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
                          <td></td>
@else
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}</td>
                          <td class="currency">{{ $figures->expected[$session->id]->deposit->count }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}</td>
                          <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}</td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
