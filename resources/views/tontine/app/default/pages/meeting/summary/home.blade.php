@php
  $rqSession = rq(Ajax\App\Meeting\Session\Session::class);
  $rqSummary = rq(Ajax\App\Meeting\Summary\Summary::class);

  $rqDeposit = rq(Ajax\App\Meeting\Summary\Pool\Deposit\Deposit::class);
  $rqRemitment = rq(Ajax\App\Meeting\Summary\Pool\Remitment\Remitment::class);
  $rqFixedFee = rq(Ajax\App\Meeting\Summary\Charge\Fixed\Fee::class);
  $rqLibreFee = rq(Ajax\App\Meeting\Summary\Charge\Libre\Fee::class);
  $rqLoan = rq(Ajax\App\Meeting\Summary\Credit\Loan\Loan::class);
  $rqRefund = rq(Ajax\App\Meeting\Summary\Credit\Refund\Refund::class);
  $rqSaving = rq(Ajax\App\Meeting\Summary\Saving\Saving::class);
  $rqProfit = rq(Ajax\App\Meeting\Summary\Saving\Profit::class);
  $rqOutflow = rq(Ajax\App\Meeting\Summary\Cash\Outflow::class);

  $menus = [];
  if($prevSession !== null)
  {
    $menus[] = [
      'class' => 'btn-session-prev',
      'text' => __('meeting.session.actions.prev'),
    ];
  }
  if($nextSession !== null)
  {
    $menus[] = [
      'class' => 'btn-session-next',
      'text' => __('meeting.session.actions.next'),
    ];
  }
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col-auto ml-auto">
@if(count($menus) > 0)
                <div class="btn-group" role="group" @jxnTarget()>
                  <div @jxnEvent(['.btn-session-prev', 'click'], $rqSummary->home($prevSession?->id ?? 0))></div>
                  <div @jxnEvent(['.btn-session-next', 'click'], $rqSummary->home($nextSession?->id ?? 0))></div>

@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'btnIcon' => 'fa-sort fa-rotate-90',
  'menus' => $menus,
])
                </div>
@endif
                <div class="btn-group ml-3" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->home())><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSummary->home($session->id))><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="row mb-2">
                <div class="col">
                  <ul class="nav nav-pills nav-fill" id="summary-tabs" @jxnOn(['a', 'click'], jq()->tab('show'))>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="summary-tab-pools" data-target="#summary-pools" role="link" tabindex="0">{!! __('meeting.actions.pools') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-charges" data-target="#summary-charges" role="link" tabindex="0">{!! __('meeting.actions.charges') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-savings" data-target="#summary-savings" role="link" tabindex="0">{!! __('meeting.actions.savings') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-refunds" data-target="#summary-refunds" role="link" tabindex="0">{!! __('meeting.actions.refunds') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-profits" data-target="#summary-profits" role="link" tabindex="0">{!! __('meeting.actions.profits') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="summary-tab-outflows" data-target="#summary-outflows" role="link" tabindex="0">{!! __('meeting.actions.outflows') !!}</a>
                    </li>
                  </nav>
                </div>
              </div>
            </div>
          </div>

          <div class="row" id="content-page">
            <div class="col">
              <div class="tab-content" id="summary-tabs-content">

                <div class="tab-pane fade show active" id="summary-pools" role="tabpanel" aria-labelledby="summary-tab-pools">
                  <div class="row sm-screen-selector mt-2 mb-1" id="summary-pools-sm-screens">
                    <div class="col-12">
                      <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                        <button data-target="content-summary-deposits" type="button" class="btn btn-primary">
                          {!! __('meeting.titles.deposits') !!}
                        </button>
                        <button data-target="content-summary-remitments" type="button" class="btn btn-outline-primary">
                          {!! __('meeting.titles.remitments') !!}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-summary-deposits">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqDeposit)>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12 sm-screen" id="content-summary-remitments">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqRemitment)>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="summary-charges" role="tabpanel" aria-labelledby="summary-tab-charges">
                  <div class="row sm-screen-selector mt-2 mb-1" id="summary-charges-sm-screens">
                    <div class="col-12">
                      <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                        <button data-target="content-summary-fees-fixed" type="button" class="btn btn-primary">
                          {!! __('meeting.charge.titles.fixed') !!}
                        </button>
                        <button data-target="content-summary-fees-libre" type="button" class="btn btn-outline-primary">
                          {!! __('meeting.charge.titles.variable') !!}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-summary-fees-fixed">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqFixedFee)>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12 sm-screen" id="content-summary-fees-libre">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqLibreFee)>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="summary-savings" role="tabpanel" aria-labelledby="summary-tab-savings">
                  <div class="row sm-screen-selector mt-2 mb-1" id="summary-savings-sm-screens">
                    <div class="col-12">
                      <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                        <button data-target="content-summary-loans" type="button" class="btn btn-primary">
                          {!! __('meeting.titles.loans') !!}
                        </button>
                        <button data-target="content-summary-savings" type="button" class="btn btn-outline-primary">
                          {!! __('meeting.titles.savings') !!}
                        </button>
                      </div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="content-summary-loans">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqLoan)>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 col-sm-12 sm-screen" id="content-summary-savings">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqSaving)>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="summary-refunds" role="tabpanel" aria-labelledby="summary-tab-refunds">
                  <div class="row">
                    <div class="col-md-12" id="content-summary-partial-refunds">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqRefund)>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="summary-profits" role="tabpanel" aria-labelledby="summary-tab-profits">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqProfit) id="content-summary-profits">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="tab-pane fade" id="summary-outflows" role="tabpanel" aria-labelledby="summary-tab-outflows">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card shadow mb-2">
                        <div class="card-body" @jxnBind($rqOutflow) id="content-summary-outflows">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              </div>
            </div>
          </div>
