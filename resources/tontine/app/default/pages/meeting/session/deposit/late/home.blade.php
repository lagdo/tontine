@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqLateDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Late\Deposit::class);
  $rqEarlyDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Early\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Late\Receivable::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.deposit.titles.late-deposits') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLateDeposit->render())><i class="fa fa-sync"></i></button>
                      </div>
                      <div class="btn-group ml-3" role="group" @jxnEvent([
                        ['.btn-session-deposits', 'click', $rqDeposit->render()],
                        ['.btn-session-early-deposits', 'click', $rqEarlyDeposit->render()],
                      ])>
@include('tontine_app::parts.table.menu', [
  'btnSize' => '',
  'menus' => [[
    'class' => 'btn-session-deposits',
    'text' => __('meeting.titles.deposits'),
  ],[
    'class' => 'btn-session-early-deposits',
    'text' => __('meeting.deposit.titles.early-deposits'),
  ]],
])
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="content-meeting-deposits" @jxnEvent(
                    ['.btn-pool-deposits', 'click', $rqReceivable->pool($poolId)])>

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
                        @include('tontine_app::pages.meeting.session.pool.' . $template, [
                          'pool' => $pool,
                          'amount' => $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre'),
                          'paid' => $pool->late_paid,
                          'count' => $pool->late_count,
                          'late' => 0,
                          'early' => 0,
                          'total' => $pool->amount_recv,
                          'menuClass' => 'btn-pool-deposits',
                        ])
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
