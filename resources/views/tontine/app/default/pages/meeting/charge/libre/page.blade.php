@php
  $chargeId = Jaxon\jq()->parent()->attr('data-charge-id')->toInt();
  $rqLibreFeePage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\LibreFeePage::class);
  $rqMember = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Member::class);
  $rqSettlement = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Settlement::class);
  $rqTarget = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Target::class);
@endphp
                  <div class="table-responsive" id="meeting-fees-libre-page" @jxnTarget()>
                    <div @jxnOn(['.btn-fee-libre-add', 'click', ''], $rqMember->charge($chargeId))></div>
                    <div @jxnOn(['.btn-fee-libre-settlements', 'click', ''], $rqSettlement->charge($chargeId))></div>
                    <div @jxnOn(['.btn-fee-libre-target', 'click', ''], $rqTarget->charge($chargeId))></div>

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
                        @include('tontine.app.default.pages.meeting.charge.libre.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqLibreFeePage)>
                    </nav>
                  </div> <!-- End table -->
