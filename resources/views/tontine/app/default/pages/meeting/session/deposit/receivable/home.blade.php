@php
  $searchValue = jq('#txt-pool-deposits-search')->val();
  $rqDeposit = rq(Ajax\App\Meeting\Session\Pool\Deposit\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Session\Pool\Deposit\Receivable::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Session\Pool\Deposit\ReceivablePage::class);
  $rqAction = rq(Ajax\App\Meeting\Session\Pool\Deposit\Action::class);
  $rqTotal = rq(Ajax\App\Meeting\Session\Pool\Deposit\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0 mb-0">{{ __('meeting.titles.deposits') }}</div>
                      <div class="section-subtitle">{{ $pool->title }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2 font-weight-bold">
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-pool-deposits-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto ml-auto pt-2">
                      <span @jxnBind($rqTotal)></span>
                      <div style="float:right;margin-left:20px;width:60px;" @if ($pool->deposit_fixed) @jxnBind($rqAction)@endif></div>
                    </div>
                  </div>

                  <div @jxnBind($rqReceivablePage)>
                  </div>
