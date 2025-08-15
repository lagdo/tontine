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
                  <div class="table-responsive" id="content-subscription-beneficiaries">
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
                        <tr>
                          <td>{{ $session['title'] }}</td>
                          <td class="currency">{{ $locale->formatMoney($session['figures']->cashier->recv) }}</td>
                          <td class="currency">{{ $session['figures']->remitment->count }} </td>
                          <td class="currency">{{ $locale->formatMoney($session['figures']->remitment->amount) }}</td>
                          <td style="flex-direction:column">
                            <div style="width:97%;" data-session-id="{{ $session['id'] }}">
@foreach ($session['payables'] as $subscriptionId)
@if ($subscriptionId === 0)
                              <div class="session-subscription-candidate" data-subscription-id="{{ $subscriptionId }}">
                              </div>
@elseif (!isset($session['beneficiaries'][$subscriptionId]))
                              <div id="session-subscription-candidate-{{ $subscriptionId }}" data-subscription-id="{{ $subscriptionId }}">
                              </div>
@else
                              <div class="input-group my-2">
                                {!! $html->text('', $session['beneficiaries'][$subscriptionId])
                                  ->class('form-control')->attribute('readonly', 'readonly')
                                  ->attribute('style', 'height:36px; padding:5px 5px;') !!}
                                <div class="input-group-append">
                                  <span class="input-group-text" style="height:36px; padding:10px;"><i class="fa fa-check"></i></span>
                                </div>
                              </div>
@endif
@endforeach
                            </div>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
