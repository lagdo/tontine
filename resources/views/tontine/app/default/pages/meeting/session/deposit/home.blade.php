@php
  $poolId = jq()->parent()->attr('data-pool-id')->toInt();
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqLateDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Late\Deposit::class);
  $rqEarlyDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Early\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-sync"></i></button>
                      </div>
                      <div class="btn-group ml-3" role="group" @jxnEvent([
                        ['.btn-session-late-deposits', 'click', $rqLateDeposit->render()],
                        ['.btn-session-early-deposits', 'click', $rqEarlyDeposit->render()],
                      ])>
@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'menus' => [[
    'class' => 'btn-session-late-deposits',
    'text' => __('meeting.deposit.titles.late-deposits'),
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
  // Early or late payments made in the current session.
  $extraPayment = $pool->prev_late + $pool->next_early;
@endphp
                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre') }}</td>
                          <td class="currency">
                            @if ($pool->paid_early > 0){{ $pool->paid_early }}+@endif<b>{{
                              $pool->paid_here }}</b>@if ($pool->paid_late > 0)+{{
                                $pool->paid_late }}@endif/<b>{{ $pool->recv_count }}</b>
                            @if ($extraPayment > 0)+{{ $extraPayment }}@endif
                            @if ($pool->recv_amount > 0)<br/>{{ $locale->formatMoney($pool->recv_amount) }}@endif
                          </td>
                          <td class="table-item-menu" data-pool-id="{{ $pool->id }}">
                            <button type="button" class="btn btn-primary btn-pool-deposits"><i class="fa fa-arrow-circle-right"></i></button>
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
