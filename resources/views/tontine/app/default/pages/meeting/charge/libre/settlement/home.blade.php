@php
  $rqCharge = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\LibreFee::class);
  $rqSettlement = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\Settlement::class);
  $rqSettlementPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Charge\Libre\SettlementPage::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $charge->name }} - {{ __('meeting.titles.settlements') }}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqCharge->render())><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" @jxnClick($rqSettlement->toggleFilter())><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-2">
                    <div class="col">
                    </div>
                    <div class="col-auto" id="meeting-settlements-total">
                    </div>
                    <div class="col-auto" id="meeting-settlements-action">
                    </div>
                  </div>
                  <div @jxnShow($rqSettlementPage)>
                  </div>
                  <nav @jxnPagination($rqSettlementPage)>
                  </nav>
