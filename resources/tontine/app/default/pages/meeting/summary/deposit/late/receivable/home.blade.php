@php
  $rqLateDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Late\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Late\Receivable::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Late\ReceivablePage::class);
  $rqTotal = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.deposit.titles.late-deposits') }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqLateDeposit->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2 font-weight-bold">
                    <div class="col-auto pt-2">{{ $pool->title }}</div>
                    <div class="col-auto ml-auto pt-2">
                      <span @jxnBind($rqTotal)></span>
                      <div style="float:right;margin-left:20px;width:60px;">&nbsp;</div>
                    </div>
                  </div>

                  <div @jxnBind($rqReceivablePage)>
                  </div>
