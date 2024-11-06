@php
  $rqPoolPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Deposit\PoolPage::class);
  $rqDeposit = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Deposit::class);
  $rqAction = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Deposit\Action::class);
  $rqTotal = Jaxon\rq(App\Ajax\Web\Meeting\Session\Pool\Deposit\Total::class);
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
                  <div @jxnShow($rqPoolPage)>
                  </div>
