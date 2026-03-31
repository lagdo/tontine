@php
  $rqSaving = rq(Ajax\App\Meeting\Session\Saving\Saving::class);
  $rqSavingPage = rq(Ajax\App\Meeting\Session\Saving\SavingPage::class);
@endphp
                  <div class="row mb-2">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                    <div class="col-auto ml-auto">
                      <div class="btn-group" role="group">
                        <button type="button" class="btn btn-primary" @jxnClick($rqSaving->render())><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div @jxnBind($rqSavingPage)>
                  </div>
