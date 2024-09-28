@php
  $rqTontine = Jaxon\rq(App\Ajax\Web\Tontine\Tontine::class);
  $rqTontinePage = Jaxon\rq(App\Ajax\Web\Tontine\TontinePage::class);
  $rqTontineGuest = Jaxon\rq(App\Ajax\Web\Tontine\Guest\Tontine::class);
  $rqSelect = Jaxon\rq(App\Ajax\Web\Tontine\Select::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.tontines') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-2 mb-2" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqTontine->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqTontine->add())><i class="fa fa-plus"></i></button>
                </div>
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSelect->showTontines())>
                    <i class="fa fa-check-square"></i> {{ __('tontine.actions.choose') }}
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body">
              <div @jxnShow($rqTontinePage)>
              </div>
              <nav @jxnPagination($rqTontinePage)>
              </nav>
            </div>
          </div>

          <div @jxnShow($rqTontineGuest)>
            @jxnHtml($rqTontineGuest)
          </div>
