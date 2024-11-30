@php
  $rqFixedFee = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Fixed\Fee::class);
  $rqFixedFeePage = Jaxon\rq(Ajax\App\Meeting\Session\Charge\Fixed\FeePage::class);
@endphp
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.charge.titles.fixed') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqFixedFee->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div @jxnBind($rqFixedFeePage)>
                  </div>
