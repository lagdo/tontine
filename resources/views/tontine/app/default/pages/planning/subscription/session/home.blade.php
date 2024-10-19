@php
  $rqSubscription = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Home::class);
  $rqBeneficiary = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Beneficiary::class);
  $rqPlanning = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Planning::class);
  $rqSession = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Session::class);
  $rqSessionCounter = Jaxon\rq(App\Ajax\Web\Planning\Subscription\SessionCounter::class);
  $rqSessionPage = Jaxon\rq(App\Ajax\Web\Planning\Subscription\SessionPage::class);
@endphp
              <div class="section-body">
                <div class="row">
                  <div class="col">
                    <h2 class="section-title">{{ $pool->title }} :: {{ __('tontine.pool.titles.sessions')
                      }} (<span @jxnShow($rqSessionCounter)>@jxnHtml($rqSessionCounter)</span>)</h2>
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2 mb-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqSession->render())><i class="fa fa-sync"></i></button>
                    </div>
                  </div>
                </div>
@if ($pool->remit_planned)
                <div class="row mb-2">
                  <div class="col">
                  </div>
                  <div class="col-auto">
                    <div class="btn-group float-right ml-2" role="group">
                      <button type="button" class="btn btn-primary" @jxnClick($rqBeneficiary->home())>{{
                        __('tontine.subscription.titles.beneficiaries') }}</i></button>
                      <button type="button" class="btn btn-primary" @jxnClick($rqPlanning->home())>{{
                        __('tontine.subscription.titles.planning') }}</i></button>
                    </div>
                  </div>
                </div>
@endif
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
