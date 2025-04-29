@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqLibreFeePage = rq(Ajax\App\Meeting\Session\Charge\Libre\FeePage::class);
  $rqMember = rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Libre\Settlement::class);
  $rqTarget = rq(Ajax\App\Meeting\Session\Charge\Libre\Target::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-libre-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-fee-libre-add', 'click'], $rqMember->charge($chargeId))></div>
                    <div @jxnEvent(['.btn-fee-libre-settlements', 'click'], $rqSettlement->charge($chargeId))></div>
                    <div @jxnEvent(['.btn-fee-libre-target', 'click'], $rqTarget->charge($chargeId))></div>

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
                        @include('tontine::pages.meeting.session.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine::pages.meeting.session.charge.libre.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqLibreFeePage)>
                    </nav>
                  </div> <!-- End table -->
