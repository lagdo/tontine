@php
  $rqSession = Jaxon\rq(App\Ajax\Web\Meeting\Session::class);
  $rqSessionPage = Jaxon\rq(App\Ajax\Web\Meeting\SessionPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.menus.sessions') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->resync()
                    ->confirm(__('tontine.session.questions.resync')))><i class="fa fa-redo"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->page())><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body">
              <div @jxnShow($rqSessionPage)>
              </div>
              <nav @jxnPagination($rqSessionPage)>
              </nav>
            </div>
          </div>
