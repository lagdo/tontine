@php
  $rqPool = rq(Ajax\App\Planning\Financial\Pool::class);
  $rqPoolFunc = rq(Ajax\App\Planning\Financial\PoolFunc::class);
  $rqPoolPage = rq(Ajax\App\Planning\Financial\PoolPage::class);
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.pools') }}</h2>
              </div>
              <div class="col-auto">
                <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->resync()
                  ->confirm(__('tontine.session.questions.resync')))>
                  <i class="fa fa-redo"></i> {{ __('tontine.session.actions.resync') }}
                </button>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqPool->render())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqPoolFunc->showIntro())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqPoolPage)>
            </div>
          </div>
