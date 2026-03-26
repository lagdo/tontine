@php
  $rqBalance = rq(Ajax\App\Report\Session\Graph\Balance::class);
  $rqInflow = rq(Ajax\App\Report\Session\Graph\Inflow::class);
  $rqOutflow = rq(Ajax\App\Report\Session\Graph\Outflow::class);
  $rqSummary = rq(Ajax\App\Report\Session\Graph\Summary::class);
@endphp
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  {{-- <div class="card-body" style="display: flex;">
                    <div style="width: 140px;">
                      <!-- This element is embedded because the Flot library changes its width. -->
                      <div id="tontine-graph-session-summary-labels">
                      </div>
                    </div>
                    <div @jxnBind($rqSummary) style="flex: 1;">
                    </div>
                  </div> --}}
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.graph.session.summary') }}</div>
                      </div>
                    </div>
                    <div style="height:100px;">
                      <!-- This element is embedded because the Flot library changes its width. -->
                      <div id="tontine-graph-session-summary-labels">
                      </div>
                    </div>
                    <div @jxnBind($rqSummary)>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.graph.session.balance') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqBalance)>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.graph.session.inflows') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqInflow)>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row mb-2">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.report.graph.session.outflows') }}</div>
                      </div>
                    </div>
                    <div @jxnBind($rqOutflow)>
                    </div>
                  </div>
                </div>
              </div>
            </div>
