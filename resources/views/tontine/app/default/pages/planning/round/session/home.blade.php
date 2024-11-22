@php
  $rqSession = Jaxon\rq(Ajax\App\Planning\Session\Session::class);
  $rqSessionPage = Jaxon\rq(Ajax\App\Planning\Session\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ __('tontine.titles.sessions') }}: {{ $round?->title ?? '' }}</h2>
                  </div>
                  <div class="col-auto sm-screen-hidden">
                    <button type="button" class="btn btn-primary" @jxnClick(Jaxon\js()->showSmScreen('content-home-rounds', 'round-sm-screens'))><i class="fa fa-arrow-left"></i></button>
                  </div>
@if ($round !== null)
                  <div class="col-auto">
                    <div class="btn-group float-right" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->home($round->id))><i class="fa fa-sync"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->add())><i class="fa fa-plus"></i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->addList())><i class="fa fa-list"></i></button>
                    </div>
                  </div>
@endif
                </div>
              </div>

              <!-- Data tables -->
              <div class="card shadow mb-4">
                <div class="card-body" @jxnShow($rqSessionPage)>
                </div>
              </div>
