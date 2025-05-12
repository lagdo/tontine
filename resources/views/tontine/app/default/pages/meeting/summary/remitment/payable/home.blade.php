@php
  $rqRemitment = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Remitment::class);
  $rqPayablePage = rq(Ajax\App\Meeting\Summary\Pool\Remitment\PayablePage::class);
  $rqTotal = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqRemitment->render())><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row p-2 font-weight-bold">
                    <div class="col-auto">
                      <div>{{ $pool->title }}</div>
                    </div>
                    <div class="col-auto ml-auto" @jxnBind($rqTotal)>
                    </div>
                  </div>

                  <div @jxnBind($rqPayablePage)>
                  </div>
