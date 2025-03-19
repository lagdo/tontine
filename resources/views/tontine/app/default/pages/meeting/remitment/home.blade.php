@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqRemitment = rq(Ajax\App\Meeting\Session\Pool\Remitment\Remitment::class);
  $rqAuction = rq(Ajax\App\Meeting\Session\Pool\Remitment\Auction::class);
  $rqPayable = rq(Ajax\App\Meeting\Session\Pool\Remitment\Payable::class);
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
                  <div class="table-responsive" id="content-session-remitments" @jxnTarget()>
                    <div @jxnEvent(['.btn-pool-remitments', 'click'], $rqPayable->pool($poolId))></div>

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
  $template = $session->closed ? 'closed' : ($session->pending ? 'pending' : 'opened');
@endphp
                        @include('tontine::pages.meeting.pool.' . $template, [
                          'pool' => $pool,
                          'amount' => !$pool->deposit_fixed ? __('tontine.labels.types.libre') :
                            $locale->formatMoney($pool->amount),
                          'paid' => $pool->pay_paid,
                          'count' => $pool->pay_count,
                          'total' => !$pool->remit_planned ? $pool->amount_paid : $pool->amount *
                            ($pool->counter->sessions - $pool->counter->disabled_sessions),
                          'menuClass' => 'btn-pool-remitments',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
