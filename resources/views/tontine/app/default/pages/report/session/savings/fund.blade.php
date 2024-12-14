@php
  $rqAmount = rq(Ajax\App\Report\Session\Saving\Amount::class);
  $rqSummary = rq(Ajax\App\Report\Session\Saving\Summary::class);
  $rqDistribution = rq(Ajax\App\Report\Session\Saving\Distribution::class);
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
                  <div class="row" @jxnBind($rqSummary)>
                    @jxnHtml($rqSummary)
                  </div>
                  <div class="row" @jxnBind($rqAmount)>
                    @jxnHtml($rqAmount)
                  </div>
                  <div class="table-responsive mt-2" id="report-fund-savings-distribution" @jxnBind($rqDistribution)>
                    @jxnHtml($rqDistribution)
                  </div>
