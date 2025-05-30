@php
  $rqOutflow = rq(Ajax\App\Meeting\Session\Cash\Outflow::class);
  $rqOutflowPage = rq(Ajax\App\Meeting\Session\Cash\OutflowPage::class);
  $rqOutflowFunc = rq(Ajax\App\Meeting\Session\Cash\OutflowFunc::class);
  $rqBalance = rq(Ajax\App\Meeting\Session\Cash\Balance::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.outflows') }}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqOutflowFunc->addOutflow())><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqOutflow->home())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col-auto ml-auto" @jxnBind($rqBalance)>
                    </div>
                  </div>
                  <div @jxnBind($rqOutflowPage)>
                  </div>
