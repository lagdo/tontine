@php
  $rqBalance = rq(Ajax\App\Report\Round\Graph\Balance::class);
  $rqInflow = rq(Ajax\App\Report\Round\Graph\Inflow::class);
  $rqOutflow = rq(Ajax\App\Report\Round\Graph\Outflow::class);
  $rqRound = rq(Ajax\App\Report\Round\Graph\Round::class);
  $rqTotal = rq(Ajax\App\Report\Round\Graph\Total::class);
@endphp
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.titles.graph.round') }}</div>
                      </div>
                    </div>
                    <div style="height:80px;">
                      <!-- This element is embedded because the Flot library changes its width. -->
                      <div id="tontine-graph-round-current-labels">
                      </div>
                    </div>
                    <div @jxnBind($rqRound)>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.titles.graph.total') }}</div>
                      </div>
                    </div>
                    <div style="height:100px;">
                      <!-- This element is embedded because the Flot library changes its width. -->
                      <div id="tontine-graph-round-total-labels">
                      </div>
                    </div>
                    <div @jxnBind($rqTotal)>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.titles.graph.balance') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqBalance)>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.titles.graph.inflows') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqInflow)>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-4 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.titles.graph.outflows') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqOutflow)>
                    </div>
                  </div>
                </div>
              </div>
            </div>
