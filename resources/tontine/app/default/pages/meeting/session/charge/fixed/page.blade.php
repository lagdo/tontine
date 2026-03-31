@php
  $chargeId = jq()->parent()->attr('data-charge-id')->toInt();
  $rqSettlement = rq(Ajax\App\Meeting\Session\Charge\Fixed\Settlement::class);
  $rqFixedFeePage = rq(Ajax\App\Meeting\Session\Charge\Fixed\FeePage::class);
@endphp
                  <div class="table-responsive" id="content-session-fees-fixed-page" @jxnEvent([
                    ['.btn-fee-fixed-settlements', 'click', $rqSettlement->charge($chargeId)]])>

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
                        @include('tontine_app::pages.meeting.session.charge.fixed.item', compact('charge', 'bills', 'settlements'))
@endif
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqFixedFeePage)>
                    </nav>
                  </div> <!-- End table -->
