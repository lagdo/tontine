@php
  $rqBillSession = Jaxon\rq(App\Ajax\Web\Report\Session\Bill\Session::class);
  $rqBillTotal = Jaxon\rq(App\Ajax\Web\Report\Session\Bill\Total::class);
  $rqDeposit = Jaxon\rq(App\Ajax\Web\Report\Session\Deposit::class);
  $rqRemitment = Jaxon\rq(App\Ajax\Web\Report\Session\Remitment::class);
  $rqDisbursement = Jaxon\rq(App\Ajax\Web\Report\Session\Disbursement::class);
  $rqLoan = Jaxon\rq(App\Ajax\Web\Report\Session\Loan::class);
  $rqRefund = Jaxon\rq(App\Ajax\Web\Report\Session\Refund::class);
  $rqSaving = Jaxon\rq(App\Ajax\Web\Report\Session\Saving::class);
  $rqSavingFund = Jaxon\rq(App\Ajax\Web\Report\Session\Saving\Fund::class);
@endphp
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-deposits" @jxnShow($rqDeposit)>
                  @jxnHtml($rqDeposit)
                </div>
                <div class="col-md-6 col-sm-12" id="report-remitments" @jxnShow($rqRemitment)>
                  @jxnHtml($rqRemitment)
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-session-bills" @jxnShow($rqBillSession)>
                  @jxnHtml($rqBillSession)
                </div>
                <div class="col-md-6 col-sm-12" id="report-total-bills" @jxnShow($rqBillTotal)>
                  @jxnHtml($rqBillTotal)
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-disbursements" @jxnShow($rqDisbursement)>
                  @jxnHtml($rqDisbursement)
                </div>
                <div class="col-md-6 col-sm-12" id="report-loans" @jxnShow($rqLoan)>
                  @jxnHtml($rqLoan)
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-refunds" @jxnShow($rqRefund)>
                  @jxnHtml($rqRefund)
                </div>
                <div class="col-md-6 col-sm-12" id="report-savings" @jxnShow($rqSaving)>
                  @jxnHtml($rqSaving)
                </div>
              </div>
              <div class="row">
                <div class="col-12" id="report-fund-savings-page" @jxnShow($rqSavingFund)>
                </div>
              </div>
