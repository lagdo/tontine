@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = Jaxon\jq()->parent()->attr('data-pool-id')->toInt();
  $rqRemitment = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Remitment::class);
  $rqAuction = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Auction::class);
  $rqPoolRemitment = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Remitment\Pool::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.remitments') !!}</div>
                    </div>
@if ($hasAuctions)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqAuction->render())>{{ __('meeting.titles.auctions') }}</button>
                      </div>
                    </div>
@endif
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRemitment->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-remitments" @jxnTarget()>
                    <div @jxnOn(['.btn-pool-remitments', 'click', ''], $rqPoolRemitment->pool($poolId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($pools as $pool)
@php
  $template = $session->disabled($pool) ? 'disabled' :
    ($session->closed ? 'closed' : ($session->pending ? 'pending' : 'opened'));
@endphp
                        @include('tontine.app.default.pages.meeting.pool.' . $template, [
                          'pool' => $pool,
                          'amount' => $pool->deposit_fixed ? $locale->formatMoney($pool->amount, true) :
                            __('tontine.labels.types.libre'),
                          'paid' => $pool->pay_paid,
                          'count' => $pool->pay_count,
                          'total' => $pool->amount_paid,
                          'menuClass' => 'btn-pool-remitments',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
