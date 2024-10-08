          <div class="section-body">
            <div class="row align-items-center">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}</h2>
              </div>
              <div class="col">
                <div class="btn-group float-right ml-1" role="group">
                  <button type="button" class="btn btn-primary" id="btn-session-back"><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-session-refresh"><i class="fa fa-sync"></i></button>
                </div>
              </div>
            </div>
          </div>

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row mb-2">
                <div class="col">
                  <ul class="nav nav-pills nav-fill" id="session-tabs">
                    <li class="nav-item" role="presentation">
                      <a class="nav-link active" id="session-tab-pools" data-target="#session-pools" href="javascript:void(0)">{!! __('meeting.actions.pools') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-charges" data-target="#session-charges" href="javascript:void(0)">{!! __('meeting.actions.charges') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-savings" data-target="#session-savings" href="javascript:void(0)">{!! __('meeting.actions.savings') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-credits" data-target="#session-credits" href="javascript:void(0)">{!! __('meeting.actions.credits') !!}</a>
                    </li>
                    <li class="nav-item" role="presentation">
                      <a class="nav-link" id="session-tab-cash" data-target="#session-cash" href="javascript:void(0)">{!! __('meeting.actions.cash') !!}</a>
                    </li>
                  </nav>
                </div>
              </div>

              <div class="row">
                <div class="col">
                  <div class="tab-content" id="session-tabs-content">
                    <div class="tab-pane fade show active" id="session-pools" role="tabpanel" aria-labelledby="session-tab-pools">
                      <div class="row sm-screen-selector mb-3" id="session-pools-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-deposits" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.deposits') !!}
                            </button>
                            <button data-target="meeting-remitments" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.remitments') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-deposits">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" id="meeting-remitments">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-charges" role="tabpanel" aria-labelledby="session-tab-charges">
                      <div class="row sm-screen-selector mb-3" id="session-charges-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-fees-fixed" type="button" class="btn btn-primary">
                              {!! __('meeting.charge.titles.fixed') !!}
                            </button>
                            <button data-target="meeting-fees-libre" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.charge.titles.variable') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-fees-fixed">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" id="meeting-fees-libre">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-savings" role="tabpanel" aria-labelledby="session-tab-savings">
                      <div class="row sm-screen-selector mb-3" id="session-savings-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-savings" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.savings') !!}
                            </button>
                            <button data-target="meeting-closings" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.closings') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-savings">
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" id="meeting-closings">
                        </div>
                        <div class="col-12 sm-screen" id="report-fund-savings">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-credits" role="tabpanel" aria-labelledby="session-tab-credits">
                      <div class="row sm-screen-selector mb-3" id="session-credits-sm-screens">
                        <div class="col-12">
                          <div class="btn-group btn-group-sm btn-block" role="group" aria-label="">
                            <button data-target="meeting-loans-col" type="button" class="btn btn-primary">
                              {!! __('meeting.titles.loans') !!}
                            </button>
                            <button data-target="meeting-refunds" type="button" class="btn btn-outline-primary">
                              {!! __('meeting.titles.refunds') !!}
                            </button>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6 col-sm-12 sm-screen sm-screen-active" id="meeting-loans-col">
                          <div id="meeting-loans">
                          </div>
                          <div id="meeting-partial-refunds">
                          </div>
                        </div>
                        <div class="col-md-6 col-sm-12 sm-screen" id="meeting-refunds">
                        </div>
                      </div>
                    </div>

                    <div class="tab-pane fade" id="session-cash" role="tabpanel" aria-labelledby="session-tab-cash">
                      <div class="row">
                        <div class="col-md-12" id="meeting-disbursements">
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
