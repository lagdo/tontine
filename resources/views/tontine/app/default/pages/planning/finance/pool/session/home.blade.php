@php
  $formValues = pm()->form('pool-session-form');
  $rqSession = rq(Ajax\App\Planning\Finance\Pool\Session::class);
  $rqSessionPage = rq(Ajax\App\Planning\Finance\Pool\SessionPage::class);
  $rqSessionFunc = rq(Ajax\App\Planning\Finance\Pool\SessionFunc::class);
  $rqSessionHeader = rq(Ajax\App\Planning\Finance\Pool\SessionHeader::class);
  $rqPool = rq(Ajax\App\Planning\Finance\Pool\Pool::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group">
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
