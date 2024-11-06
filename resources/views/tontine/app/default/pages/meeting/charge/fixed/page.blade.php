@php
  $chargeId = Jaxon\jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Fixed\Settlement::class);
  $rqFixedFeePage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\FixedFeePage::class);
@endphp
                  <div class="table-responsive" id="meeting-fees-fixed-page" @jxnTarget()>
                    <div @jxnOn(['.btn-fee-fixed-settlements', 'click', ''], $rqSettlement->charge($chargeId))></div>

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
                        @include('tontine.app.default.pages.meeting.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine.app.default.pages.meeting.charge.fixed.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFixedFeePage)>
                    </nav>
                  </div> <!-- End table -->
