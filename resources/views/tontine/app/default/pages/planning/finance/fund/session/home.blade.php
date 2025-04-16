@php
  $formValues = pm()->form('fund-session-form');
  $rqSession = rq(Ajax\App\Planning\Finance\Fund\Session::class);
  $rqSessionPage = rq(Ajax\App\Planning\Finance\Fund\SessionPage::class);
  $rqSessionFunc = rq(Ajax\App\Planning\Finance\Fund\SessionFunc::class);
  $rqSessionHeader = rq(Ajax\App\Planning\Finance\Fund\SessionHeader::class);
  $rqFund = rq(Ajax\App\Planning\Finance\Fund\Fund::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ $fund->title }} :: {{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-primary" @jxnClick($rqFund->render())><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2 mb-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->save($formValues))><i class="fa fa-save"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-header" @jxnBind($rqSessionHeader)>
            </div>
            <div class="card-body" @jxnBind($rqSessionPage)>
            </div>
          </div>
