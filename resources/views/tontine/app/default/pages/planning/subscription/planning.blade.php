@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <h2 class="section-title">{{ __('tontine.subscription.titles.planning') }}</h2>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-subscription-beneficiaries">{{
                          __('tontine.subscription.titles.beneficiaries') }}</i></button>
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
                  </div>
