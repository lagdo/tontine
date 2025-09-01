@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqRemitment = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Remitment::class);
  $rqAuction = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Auction::class);
  $rqPayable = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Payable::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.remitments') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRemitment->render())><i class="fa fa-sync"></i></button>
                      </div>
@if ($hasAuctions)
                      <div class="btn-group ml-3" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqAuction->render())>{{ __('meeting.titles.auctions') }}</button>
                      </div>
@endif
                    </div>
                  </div>
                  <div class="table-responsive" id="content-session-remitments" @jxnEvent([
                    ['.btn-pool-remitments', 'click', $rqPayable->pool($poolId)]])>

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
                        @include('tontine::pages.meeting.summary.pool.' . $template, [
                          'pool' => $pool,
                          'amount' => !$pool->deposit_fixed ? __('tontine.labels.types.libre') :
                            $locale->formatMoney($pool->amount),
                          'paid' => $pool->pay_paid,
                          'count' => $pool->pay_count,
                          'late' => 0,
                          'early' => 0,
                          'total' => !$pool->remit_planned ? $pool->amount_paid : $pool->amount *
                            ($pool->sessions->count() - $pool->disabled_sessions->count()),
                          'menuClass' => 'btn-pool-remitments',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
