@php
  $rqBillSession = Jaxon\rq(Ajax\App\Report\Session\Bill\Session::class);
  $rqBillTotal = Jaxon\rq(Ajax\App\Report\Session\Bill\Total::class);
  $rqDeposit = Jaxon\rq(Ajax\App\Report\Session\Deposit::class);
  $rqRemitment = Jaxon\rq(Ajax\App\Report\Session\Remitment::class);
  $rqDisbursement = Jaxon\rq(Ajax\App\Report\Session\Disbursement::class);
  $rqLoan = Jaxon\rq(Ajax\App\Report\Session\Loan::class);
  $rqRefund = Jaxon\rq(Ajax\App\Report\Session\Refund::class);
  $rqSaving = Jaxon\rq(Ajax\App\Report\Session\Saving::class);
  $rqSavingFund = Jaxon\rq(Ajax\App\Report\Session\Saving\Fund::class);
@endphp
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-deposits" @jxnShow($rqDeposit)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-remitments" @jxnShow($rqRemitment)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-session-bills" @jxnShow($rqBillSession)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-total-bills" @jxnShow($rqBillTotal)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-disbursements" @jxnShow($rqDisbursement)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-loans" @jxnShow($rqLoan)>
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-refunds" @jxnShow($rqRefund)>
                </div>
                <div class="col-md-6 col-sm-12" id="report-savings" @jxnShow($rqSaving)>
                </div>
              </div>
              <div class="row">
                <div class="col-12" id="report-fund-savings-page" @jxnShow($rqSavingFund)>
                </div>
              </div>
