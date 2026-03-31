@php
  $rqRound = rq(Ajax\App\Guild\Calendar\Round::class);
  $rqRoundFunc = rq(Ajax\App\Guild\Calendar\RoundFunc::class);
  $rqRoundPage = rq(Ajax\App\Guild\Calendar\RoundPage::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('tontine.titles.rounds') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group" role="group">
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
