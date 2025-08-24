@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Deposit::class);
  $rqLateDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Late\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Receivable::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-sync"></i></button>
                      </div>
                      <div class="btn-group ml-3" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLateDeposit->render())>{{ __('meeting.deposit.titles.late-deposits') }}</button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-meeting-deposits" @jxnEvent([
                    ['.btn-pool-deposits', 'click', $rqReceivable->pool($poolId)]])>

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
                          'amount' => $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre'),
                          'paid' => $pool->recv_paid,
                          'count' => $pool->recv_count,
                          'late' => $pool->recv_late,
                          'early' => $pool->recv_early,
                          'total' => $pool->amount_recv,
                          'menuClass' => 'btn-pool-deposits',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
