@php
  $rqFixedFee = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Fee::class);
  $rqFixedFeePage = rq(Ajax\App\Meeting\Summary\Charge\Fixed\FeePage::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.charge.titles.fixed') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqFixedFee->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div @jxnBind($rqFixedFeePage)>
                  </div>
