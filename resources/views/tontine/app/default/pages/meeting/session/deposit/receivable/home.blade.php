@php
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAction = rq(Ajax\App\Meeting\Session\Pool\Deposit\Action::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Pool\Deposit\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2 font-weight-bold">
                    <div class="col">
                      <div>{{ $pool->title }}</div>
                    </div>
                    <div class="col-auto">
                      <span @jxnBind($rqTotal)></span>
                    </div>
@if ($pool->deposit_fixed)
                    <div class="col-auto">
                      <span style="display:inline-block;width:60px;" @jxnBind($rqAction)></span>
                    </div>
@endif
                  </div>

                  <div @jxnBind($rqReceivablePage)>
                  </div>
