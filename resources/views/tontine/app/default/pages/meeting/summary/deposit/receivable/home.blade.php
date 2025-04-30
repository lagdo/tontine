@php
  $searchValue = jq('#txt-pool-deposits-search')->val();
  $rqDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Deposit::class);
  $rqReceivable = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Receivable::class);
  $rqReceivablePage = rq(Ajax\App\Meeting\Summary\Pool\Deposit\ReceivablePage::class);
  $rqTotal = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Total::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ $pool->title }} - {{ __('meeting.titles.deposits') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqDeposit->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2 font-weight-bold">
                    <div class="col">
                      <div class="input-group">
                        {!! $html->text('search', '')->id('txt-pool-deposits-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqReceivable->search($searchValue))><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto pt-2">
                      <span @jxnBind($rqTotal)></span>
                    </div>
                    <div class="col-auto pt-2">
                      <span style="display:inline-block;width:60px;"></span>
                    </div>
                  </div>

                  <div @jxnBind($rqReceivablePage)>
                  </div>
