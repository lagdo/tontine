@php
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Session\SessionPage::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.titles.sessions') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->resync()
                    ->confirm(__('tontine.session.questions.resync')))>
                    <i class="fa fa-redo"></i> {{ __('tontine.session.actions.resync') }}
                  </button>
                </div>
                <div class="btn-group ml-3" role="group">
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
