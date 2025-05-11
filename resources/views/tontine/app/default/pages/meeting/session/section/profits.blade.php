@php
  $rqProfit = rq(Ajax\App\Meeting\Session\Saving\Profit::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
@include('tontine::pages.meeting.session.section.action', [
  'sectionTitle' => __('meeting.actions.credits'),
])
            </div>
          </div>

          <div class="card shadow mb-2">
            <div class="card-body">
              <div class="row">
                <div class="col-md-12" @jxnBind($rqProfit)>
                </div>
              </div>
            </div>
          </div>
