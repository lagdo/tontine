          <div class="section-body">
            <div class="row align-items-center">
              <div class="col">
                <h2 class="section-title" id="session-report-title"></h2>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ Form::select('session_id', $sessions, 0, ['class' => 'form-control', 'id' => 'select-session']) }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-session-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto" id="session-reports-export">
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ Form::select('member_id', $members, 0, ['class' => 'form-control', 'id' => 'select-member']) }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" id="btn-member-select"><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-deposits">
                </div>
                <div class="col-md-6 col-sm-12" id="report-remitments">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-session-bills">
                </div>
                <div class="col-md-6 col-sm-12" id="report-total-bills">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-disbursements">
                </div>
                <div class="col-md-6 col-sm-12" id="report-loans">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="report-refunds">
                </div>
                <div class="col-md-6 col-sm-12" id="report-savings">
                </div>
              </div>
              <div class="row">
                <div class="col-12" id="report-fund-savings">
                </div>
              </div>
            </div>
          </div>
