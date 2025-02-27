@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Fixed\Settlement::class);
  $rqFixedFeePage = rq(Ajax\App\Meeting\Session\Charge\Fixed\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-fixed-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-fee-fixed-settlements', 'click'], $rqSettlement->charge($chargeId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($charges as $charge)
@if($session->pending)
                        @include('tontine::pages.meeting.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine::pages.meeting.charge.fixed.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFixedFeePage)>
                    </nav>
                  </div> <!-- End table -->
