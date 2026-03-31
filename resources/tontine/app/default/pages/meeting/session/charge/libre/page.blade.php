@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqLibreFeePage = rq(Ajax\App\Meeting\Session\Charge\Libre\FeePage::class);
  $rqMember = rq(Ajax\App\Meeting\Session\Charge\Libre\Member::class);
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Libre\Settlement::class);
  $rqSaving = rq(Ajax\App\Meeting\Session\Charge\Libre\Saving::class);
  $rqTarget = rq(Ajax\App\Meeting\Session\Charge\Libre\Target::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-libre-page" @jxnEvent([
                    ['.btn-fee-libre-add', 'click', $rqMember->charge($chargeId)],
                    ['.btn-fee-libre-settlements', 'click', $rqSettlement->charge($chargeId)],
                    ['.btn-fee-libre-savings', 'click', $rqSaving->charge($chargeId)],
                    ['.btn-fee-libre-target', 'click', $rqTarget->charge($chargeId)],
                  ])>

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
                        @include('tontine_app::pages.meeting.session.charge.pending', compact('charge', 'bills', 'settlements'))
@else
                        @include('tontine_app::pages.meeting.session.charge.libre.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqLibreFeePage)>
                    </nav>
                  </div> <!-- End table -->
