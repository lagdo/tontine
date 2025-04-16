@php
  $rqDistribution = rq(Ajax\App\Report\Session\Saving\Distribution::class);
  $rqAmount = rq(Ajax\App\Report\Session\Saving\Amount::class);
  $rqTotal = rq(Ajax\App\Report\Session\Saving\Distribution\Total::class);
  $rqParts = rq(Ajax\App\Report\Session\Saving\Distribution\Parts::class);
  $rqBasis = rq(Ajax\App\Report\Session\Saving\Distribution\Basis::class);
@endphp
                  <div class="row">
                    <div class="col py-2 font-weight-bold" @jxnBind($rqTotal)>
                      @jxnHtml($rqTotal)
                    </div>
                    <div class="col-auto py-2 font-weight-bold" @jxnBind($rqParts)>
                      @jxnHtml($rqParts)
                    </div>
                    <div class="col-auto py-2 font-weight-bold" @jxnBind($rqBasis)>
                      @jxnHtml($rqBasis)
                    </div>
                    <div class="col-auto" @jxnBind($rqAmount)>
                      @jxnHtml($rqAmount)
                    </div>
                  </div>
                  <div class="table-responsive mt-2" id="content-report-profit-distribution" @jxnBind($rqDistribution)>
                    @jxnHtml($rqDistribution)
                  </div>
