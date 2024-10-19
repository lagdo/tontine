@php
  $rqPoolRound = Jaxon\rq(App\Ajax\Web\Planning\PoolRound::class);
  $rqPoolRoundAction = Jaxon\rq(App\Ajax\Web\Planning\PoolRoundAction::class);
  $rqPoolRoundStartSession = Jaxon\rq(App\Ajax\Web\Planning\PoolRoundStartSession::class);
  $rqPoolRoundEndSession = Jaxon\rq(App\Ajax\Web\Planning\PoolRoundEndSession::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.pool_round.titles.sessions', ['pool' => $pool->title]) }}</h2>
              </div>
              <div class="col-auto" @jxnShow($rqPoolRoundAction)>
              </div>
            </div>
          </div>
          <div class="row sm-screen-selector mb-3" id="pool-round-sessions-sm-screens-btn">
            <div class="col-12">
              <div class="btn-group btn-group-sm btn-block" role="group">
                <button data-target="pool-round-sessions-start-screen" type="button" class="btn btn-primary">
                  {!! __('tontine.pool_round.titles.start') !!}
                </button>
                <button data-target="pool-round-sessions-end-screen" type="button" class="btn btn-outline-primary">
                  {!! __('tontine.pool_round.titles.end') !!}
                </button>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body">
              <form id="round-form">
                <div class="row" id="pool-round-sessions-sm-screens">
                  <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="pool-round-sessions-start-screen">
                    <div class="row">
                      <div class="col">
                        <h2 class="section-title" id="pool-round-start-session-title"></h2>
                      </div>
                      <div class="col-auto">
                        <div class="btn-group float-right" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqPoolRoundStartSession->showSessionPage())><i class="fa fa-arrow-circle-down"></i></button>
                        </div>
                      </div>
                    </div>

                    <div @jxnShow($rqPoolRoundStartSession) id="pool-round-sessions-start">
                    </div>
                    <nav @jxnPagination($rqPoolRoundStartSession)>
                    </nav>
                  </div>
                  <div class="col-md-6 col-sm-12 sm-screen" id="pool-round-sessions-end-screen">
                    <div class="row">
                      <div class="col">
                        <h2 class="section-title" id="pool-round-end-session-title"></h2>
                      </div>
                      <div class="col-auto">
                        <div class="btn-group float-right" role="group">
                          <button type="button" class="btn btn-primary" @jxnClick($rqPoolRoundEndSession->showSessionPage())><i class="fa fa-arrow-circle-down"></i></button>
                        </div>
                      </div>
                    </div>

                    <div @jxnShow($rqPoolRoundEndSession) id="pool-round-sessions-end">
                    </div>
                    <nav @jxnPagination($rqPoolRoundEndSession)>
                    </nav>
                  </div>
                </div>
              </form>
            </div>
          </div>
