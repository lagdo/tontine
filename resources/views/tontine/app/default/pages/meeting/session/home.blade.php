@php
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Session\SessionPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->resync()
                    ->confirm(__('tontine.session.questions.resync')))><i class="fa fa-redo"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionPage->page())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqSessionPage)>
            </div>
          </div>
