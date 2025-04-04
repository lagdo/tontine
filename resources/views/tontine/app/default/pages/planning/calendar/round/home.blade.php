@php
  $rqRound = rq(Ajax\App\Planning\Calendar\Round::class);
  $rqRoundFunc = rq(Ajax\App\Planning\Calendar\RoundFunc::class);
  $rqRoundPage = rq(Ajax\App\Planning\Calendar\RoundPage::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title">{{ __('tontine.titles.rounds') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqRound->home())><i class="fa fa-sync"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqRoundFunc->add())><i class="fa fa-plus"></i></button>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" @jxnBind($rqRoundPage)>
            </div>
          </div>
