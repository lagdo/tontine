@php
  $rqBillSession = rq(Ajax\App\Report\Session\Table\BillSession::class);
  $rqBillTotal = rq(Ajax\App\Report\Session\Table\BillTotal::class);
  $rqDeposit = rq(Ajax\App\Report\Session\Table\Deposit::class);
  $rqRemitment = rq(Ajax\App\Report\Session\Table\Remitment::class);
  $rqOutflow = rq(Ajax\App\Report\Session\Table\Outflow::class);
  $rqLoan = rq(Ajax\App\Report\Session\Table\Loan::class);
  $rqRefund = rq(Ajax\App\Report\Session\Table\Refund::class);
  $rqSaving = rq(Ajax\App\Report\Session\Table\Saving::class);
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
                  <div class="card-body" id="content-report-loans" @jxnBind($rqLoan)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-refunds" @jxnBind($rqRefund)>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-savings" @jxnBind($rqSaving)>
                  </div>
                </div>
              </div>
              <div class="col-md-6 col-sm-12">
                <div class="card shadow mb-4">
                  <div class="card-body" id="content-report-outflows" @jxnBind($rqOutflow)>
                  </div>
                </div>
              </div>
            </div>
