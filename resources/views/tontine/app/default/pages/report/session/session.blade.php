@php
  $rqBillSession = rq(Ajax\App\Report\Session\Bill\Session::class);
  $rqBillTotal = rq(Ajax\App\Report\Session\Bill\Total::class);
  $rqDeposit = rq(Ajax\App\Report\Session\Deposit::class);
  $rqRemitment = rq(Ajax\App\Report\Session\Remitment::class);
  $rqDisbursement = rq(Ajax\App\Report\Session\Disbursement::class);
  $rqLoan = rq(Ajax\App\Report\Session\Loan::class);
  $rqRefund = rq(Ajax\App\Report\Session\Refund::class);
  $rqSaving = rq(Ajax\App\Report\Session\Saving::class);
  $rqSavingFund = rq(Ajax\App\Report\Session\Saving\Fund::class);
@endphp
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-deposits" @jxnBind($rqDeposit)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-remitments" @jxnBind($rqRemitment)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-session-bills" @jxnBind($rqBillSession)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-total-bills" @jxnBind($rqBillTotal)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-disbursements" @jxnBind($rqDisbursement)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-loans" @jxnBind($rqLoan)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-refunds" @jxnBind($rqRefund)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-savings" @jxnBind($rqSaving)>
                </div>
              </div>
              <div class="row">
                <div class="col-12" id="report-fund-savings-page" @jxnBind($rqSavingFund)>
                </div>
              </div>
