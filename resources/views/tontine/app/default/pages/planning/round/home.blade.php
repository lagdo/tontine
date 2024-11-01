@php
  $rqSelect = Jaxon\rq(App\Ajax\Web\Tontine\Select::class);
  $rqRound = Jaxon\rq(App\Ajax\Web\Planning\Round::class);
  $rqRoundPage = Jaxon\rq(App\Ajax\Web\Planning\RoundPage::class);
  $rqSession = Jaxon\rq(App\Ajax\Web\Planning\Session::class);
@endphp
          <div class="row" id="round-sm-screens">
            <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-home-rounds">
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.titles.rounds') }}</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSelect->showRounds())>
                        <i class="fa fa-check-square"></i> {{ __('tontine.actions.choose') }}
                      </button>
                    </div>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqRound->home())><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqRound->add())><i class="fa fa-plus"></i></button>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body">
                  <div @jxnShow($rqRoundPage)>
                  </div>
                  <nav @jxnPagination($rqRoundPage)>
                  </nav>
                </div>
              </div>
            </div>

            <div @jxnShow($rqSession) class="col-md-6 col-sm-12 sm-screen" id="content-home-sessions">
            </div>
          </div>
