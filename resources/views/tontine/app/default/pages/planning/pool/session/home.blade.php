@php
  $formValues = pm()->form('pool-session-form');
  $rqSession = rq(Ajax\App\Planning\Pool\Session::class);
  $rqSessionPage = rq(Ajax\App\Planning\Pool\SessionPage::class);
  $rqSessionFunc = rq(Ajax\App\Planning\Pool\SessionFunc::class);
  $rqSessionHeader = rq(Ajax\App\Planning\Pool\SessionHeader::class);
  $rqPool = rq(Ajax\App\Planning\Pool\Pool::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-arrow-left"></i></button>
                </div>
                <div class="btn-group ml-3" role="group">
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
