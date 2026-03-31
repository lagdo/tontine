@php
  $rqRefund = rq(Ajax\App\Meeting\Session\Credit\Refund\Refund::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
@include('tontine_app::pages.meeting.session.section.action', [
  'sectionTitle' => __('meeting.actions.credits'),
])
            </div>
          </div>

          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" id="content-session-refunds" @jxnBind($rqRefund)>
                </div>
              </div>
            </div>
          </div>
