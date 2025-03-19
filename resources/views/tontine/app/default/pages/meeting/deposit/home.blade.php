@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-meeting-deposits" @jxnTarget()>
                    <div @jxnEvent(['.btn-pool-deposits', 'click'], $rqReceivable->pool($poolId))></div>

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
                          'amount' => $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre'),
                          'paid' => $pool->recv_paid,
                          'count' => $pool->recv_count,
                          'total' => $pool->amount_recv,
                          'menuClass' => 'btn-pool-deposits',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
