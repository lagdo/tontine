@php
  $rqFinance = rq(Ajax\App\Planning\Finance::class);
  $rqBeneficiary = rq(Ajax\App\Planning\Pool\Subscription\Beneficiary::class);
  $rqPlanning = rq(Ajax\App\Planning\Pool\Subscription\Planning::class);
  $poolSessionIds = $pool->sessions->pluck('id', 'id');
@endphp
            <div class="col-md-12">
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col">
                    <h2 class="section-title">
                      {{ __('tontine.subscription.titles.beneficiaries') }}:
                      {{ $pool->title . ' - ' . ($pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre')) }}
                    </h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqPlanning->render())>{{
                        __('tontine.subscription.titles.planning') }}</i></button>
                    </div>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqFinance->render())><i class="fa fa-arrow-left"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqBeneficiary->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div class="table-responsive" id="content-subscription-beneficiaries" @jxnTarget()>
                    <div @jxnEvent(['.select-beneficiary', 'change'], $rqBeneficiary->save(
                        jq()->attr('data-session-id')->toInt(),
                        jq()->val()->toInt(),
                        jq()->attr('data-subscription-id')->toInt()
                      ))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('figures.titles.session') }}</th>
                          <th class="currency">{{ __('figures.titles.amount') }}</th>
                          <th class="currency">{{ __('figures.remitment.titles.count') }}</th>
                          <th class="currency">{{ __('figures.remitment.titles.amount') }}</th>
                          <th>{{ __('figures.titles.beneficiaries') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($sessions as $session)
@if (!$poolSessionIds->has($session->id))
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
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv) }}</td>
                          <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount) }}</td>
                          <td style="flex-direction:column"><div style="width:97%;">
@foreach ($session->beneficiaries as $subscription)
@php
  $items = $subscriptions;
  if($subscription > 0 && isset($beneficiaries[$subscription]))
  {
    $items = collect($subscriptions->all())->put($subscription, $beneficiaries[$subscription]);
  }
@endphp
                            {!! $html->select('', $items, $subscription)->class('form-control my-2 select-beneficiary')
                              ->attributes(['data-session-id' => $session->id, 'data-subscription-id' => $subscription]) !!}
@endforeach
                          </div></td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
