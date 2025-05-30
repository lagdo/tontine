@php
  $rqFinance = rq(Ajax\App\Planning\Finance::class);
  $rqBeneficiary = rq(Ajax\App\Planning\Pool\Subscription\Beneficiary::class);
  $rqPlanning = rq(Ajax\App\Planning\Pool\Subscription\Planning::class);
  $poolSessionIds = $pool->sessions->pluck('id', 'id');
@endphp
            <div class="col-md-12">
              <div class="section-body">
                <div class="row mb-2">
                  <div class="col-auto">
                    <h2 class="section-title">
                      {{ __('tontine.subscription.titles.planning') }}:
                      {{ $pool->title . ' - ' . ($pool->deposit_fixed ?
                        $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre')) }}
                    </h2>
                  </div>
                  <div class="col-auto ml-auto">
                    <div class="btn-group" role="group">
@if ($pool->remit_planned)
                      <button type="button" class="btn btn-primary" @jxnClick($rqBeneficiary->render())>{{
                        __('tontine.subscription.titles.beneficiaries') }}</i></button>
                    </div>
                    <div class="btn-group ml-3" role="group">
@endif
                      <button type="button" class="btn btn-primary" @jxnClick($rqFinance->render())><i class="fa fa-arrow-left"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqPlanning->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <div class="card shadow mb-4">
                <div class="card-body">
                  <!-- Data tables -->
                  <div class="table-responsive" id="content-subscription-planning">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('figures.titles.session') !!}</th>
                          <th class="currency">{!! __('figures.titles.start') !!}</th>
                          <th class="currency">{!! __('figures.titles.deposits') !!}</th>
                          <th class="currency">{!! __('figures.titles.recv') !!}</th>
                          <th class="currency">{!! __('figures.titles.remitments') !!}</th>
                          <th class="currency">{!! __('figures.titles.end') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($sessions as $session)
@if($poolSessionIds->has($session->id))
                        <tr>
                          <td><b>{{ $session->title }}</b></td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->start, false) }}</td>
                          <td class="currency">
                            {{ $figures->expected[$session->id]->deposit->count }} /
                            {{ $locale->formatMoney($figures->expected[$session->id]->deposit->amount, false) }}
                          </td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->recv, false) }}</td>
                          <td class="currency">
                            {{ $figures->expected[$session->id]->remitment->count }} /
                            {{ $locale->formatMoney($figures->expected[$session->id]->remitment->amount, false) }}
                          </td>
                          <td class="currency">{{ $locale->formatMoney($figures->expected[$session->id]->cashier->end, false) }}</td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
                </div>
              </div>
            </div>
