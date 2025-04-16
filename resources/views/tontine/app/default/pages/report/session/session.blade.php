@php
  $rqBillSession = rq(Ajax\App\Report\Session\Bill\Session::class);
  $rqBillTotal = rq(Ajax\App\Report\Session\Bill\Total::class);
  $rqDeposit = rq(Ajax\App\Report\Session\Deposit::class);
  $rqRemitment = rq(Ajax\App\Report\Session\Remitment::class);
  $rqOutflow = rq(Ajax\App\Report\Session\Outflow::class);
  $rqLoan = rq(Ajax\App\Report\Session\Loan::class);
  $rqRefund = rq(Ajax\App\Report\Session\Refund::class);
  $rqSaving = rq(Ajax\App\Report\Session\Saving::class);
  $rqProfit = rq(Ajax\App\Report\Session\Saving\Profit::class);
@endphp
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-deposits" @jxnBind($rqDeposit)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-remitments" @jxnBind($rqRemitment)>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-session-bills" @jxnBind($rqBillSession)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-total-bills" @jxnBind($rqBillTotal)>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-outflows" @jxnBind($rqOutflow)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-loans" @jxnBind($rqLoan)>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-refunds" @jxnBind($rqRefund)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-savings" @jxnBind($rqSaving)>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <div class="card shadow mb-4">
                  <div class="card-body">
                    <div class="row">
                      <div class="col">
                        <div class="section-title mt-0" id="content-report-profits-fund"></div>
                      </div>
                    </div>
                    <div id="content-report-profits" @jxnBind($rqProfit)>
                    </div>
                  </div>
                </div>
              </div>
            </div>
