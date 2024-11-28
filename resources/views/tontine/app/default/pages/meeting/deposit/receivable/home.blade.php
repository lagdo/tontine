@php
  $rqDeposit = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqReceivablePage = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAction = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Action::class);
  $rqTotal = Jaxon\rq(Ajax\App\Meeting\Session\Pool\Deposit\Total::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $pool->title }}<br/> {{ __('meeting.titles.deposits')
                          }} (<span @jxnShow($rqTotal)></span>) <span @jxnShow($rqAction)></span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div @jxnShow($rqReceivablePage)>
                  </div>
