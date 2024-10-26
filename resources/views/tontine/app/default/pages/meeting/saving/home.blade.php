@php
  $selectFundId = Jaxon\pm()->select('savings-fund-id')->toInt();
  $rqSaving = Jaxon\rq(App\Ajax\Web\Meeting\Session\Saving\Saving::class);
  $rqSavingPage = Jaxon\rq(App\Ajax\Web\Meeting\Session\Saving\SavingPage::class);
  $rqMember = Jaxon\rq(App\Ajax\Web\Meeting\Session\Saving\Member::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col" id="meeting-savings-total">
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund', $funds, $fundId)->id('savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqSaving->fund($selectFundId))><i class="fa fa-arrow-right"></i></button>
                          <button type="button" class="btn btn-primary" @jxnClick($rqMember->home($selectFundId)->ifgt($selectFundId, 0))><i class="fa fa-edit"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div @jxnShow($rqSavingPage)>
                  </div>
                  <nav @jxnPagination($rqSavingPage)>
                  </nav>
