@php
  $rqOutflow = rq(Ajax\App\Meeting\Session\Cash\Outflow::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
@include('tontine::pages.meeting.session.section.action', [
  'sectionTitle' => __('meeting.actions.outflows'),
])
            </div>
          </div>

          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" id="content-session-outflows" @jxnBind($rqOutflow)>
                </div>
              </div>
            </div>
          </div>
