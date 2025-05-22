@php
  $rqFinance = rq(Ajax\App\Planning\Finance::class);
  $rqBeneficiary = rq(Ajax\App\Planning\Pool\Subscription\Beneficiary::class);
  $rqPlanning = rq(Ajax\App\Planning\Pool\Subscription\Planning::class);
@endphp
            <div class="col-md-12">
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">
                      {{ __('tontine.subscription.titles.beneficiaries') }}:
                      {{ $pool->title . ' - ' . ($pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre')) }}
                    </h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqPlanning->render())>{{
                        __('tontine.subscription.titles.planning') }}</i></button>
                    </div>
                    <div class="btn-group ml-3" role="group">
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
@php
  $payables = $session->payables->keyBy('subscription_id');
@endphp
                        <tr>
                          <td>{{ $session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv) }}</td>
                          <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount) }}</td>
                          <td style="flex-direction:column"><div style="width:97%;">
@foreach ($session->beneficiaries as $subscriptionId)
@if (!($payables[$subscriptionId]?->remitment ?? null))
@php
  $items = $subscriptions;
  if($subscriptionId > 0 && isset($beneficiaries[$subscriptionId]))
  {
    $items = collect($subscriptions->all())
      ->put($subscriptionId, $beneficiaries[$subscriptionId]);
  }
@endphp
                            {!! $html->select('', $items, $subscriptionId)
                              ->class('form-control my-2 select-beneficiary')
                              ->attribute('data-session-id', $session->id)
                              ->attribute('data-subscription-id', $subscriptionId)
                              ->attribute('style', 'height:36px; padding:5px 5px;') !!}
@else
                            <div class="input-group my-2">
                              {!! $html->text('', $beneficiaries[$subscriptionId])
                                ->class('form-control')->attribute('readonly', 'readonly')
                                ->attribute('style', 'height:36px; padding:5px 5px;') !!}
                              <div class="input-group-append">
                                <span class="input-group-text" style="height:36px; padding:10px;"><i class="fa fa-check"></i></span>
                              </div>
                            </div>
@endif
@endforeach
                          </div></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
