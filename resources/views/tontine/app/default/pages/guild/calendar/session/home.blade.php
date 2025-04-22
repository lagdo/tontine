@php
  $rqSession = rq(Ajax\App\Guild\Calendar\Session::class);
  $rqSessionFunc = rq(Ajax\App\Guild\Calendar\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Guild\Calendar\SessionPage::class);
  $rqRound = rq(Ajax\App\Guild\Calendar\Round::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.sessions') }}: {{ $round->title }}</h2>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-primary" @jxnClick($rqRound->render())><i class="fa fa-arrow-left"></i></button>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->add())><i class="fa fa-plus"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->addList())><i class="fa fa-list"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqSessionPage)>
            </div>
          </div>
