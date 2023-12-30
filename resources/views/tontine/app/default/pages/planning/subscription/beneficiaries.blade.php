@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <h2 class="section-title">{{ __('tontine.subscription.titles.beneficiaries') }}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-planning">{{
                          __('tontine.subscription.titles.planning') }}</i></button>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-subscription-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>

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
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, true) }}</td>
                          <td class="currency">{{ $figures->expected[$session->id]->remitment->count }}</td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, true) }}</td>
                          <td>
@foreach ($session->beneficiaries as $subscription)
@php
  $items = $subscriptions;
  if($subscription > 0 && isset($beneficiaries[$subscription]))
  {
    $items = collect($subscriptions->all())->put($subscription, $beneficiaries[$subscription]);
  }
@endphp
                            {!! Form::select('', $items, $subscription, [
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
