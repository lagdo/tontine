@php
  $rqEarlyDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Early\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Early\Receivable::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\Early\ReceivablePage::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Pool\Deposit\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0 mb-0">{{ __('meeting.deposit.titles.early-deposits') }}</div>
                      <div class="section-subtitle">{{ $pool->title }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqEarlyDeposit->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2 font-weight-bold">
                    <div class="col-auto pt-2">{{ $session->title }}</div>
                    <div class="col-auto ml-auto pt-2">
                      <span @jxnBind($rqTotal)></span>
                      <div style="float:right;margin-left:20px;width:60px;">&nbsp;</div>
                    </div>
                  </div>

                  <div @jxnBind($rqReceivablePage)>
                  </div>
