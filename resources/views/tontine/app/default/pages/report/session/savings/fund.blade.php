@php
  $rqAmount = Jaxon\rq(Ajax\App\Report\Session\Saving\Amount::class);
  $rqSummary = Jaxon\rq(Ajax\App\Report\Session\Saving\Summary::class);
  $rqDistribution = Jaxon\rq(Ajax\App\Report\Session\Saving\Distribution::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! $fund->title !!}</div>
                    </div>
@if ($backButton)
                    <div class="col-auto sm-screen-hidden">
                      <button type="button" class="btn btn-primary" id="btn-presence-sessions-back"><i class="fa fa-arrow-left"></i></button>
                    </div>
@endif
                  </div>
                  <div class="row" @jxnShow($rqSummary)>
                    @jxnHtml($rqSummary)
                  </div>
                  <div class="row" @jxnShow($rqAmount)>
                    @jxnHtml($rqAmount)
                  </div>
                  <div class="table-responsive mt-2" id="report-fund-savings-distribution" @jxnShow($rqDistribution)>
                    @jxnHtml($rqDistribution)
                  </div>
