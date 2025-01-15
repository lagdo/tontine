@php
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAction = rq(Ajax\App\Meeting\Session\Pool\Deposit\Action::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Pool\Deposit\Total::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $pool->title }}<br/> {{ __('meeting.titles.deposits')
                          }} (<span @jxnBind($rqTotal)></span>) <span @jxnBind($rqAction)></span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqReceivablePage)>
                  </div>
