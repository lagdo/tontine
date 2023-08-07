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
                      <a type="button" class="btn btn-primary" target="_blank" href="javascript:void(0)"><i class="fa fa-file-pdf"></i></a>
                  </div>
                </div>
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
                <div class="col-md-6 col-sm-12" id="member-deposits">
                </div>
                <div class="col-md-6 col-sm-12" id="member-remitments">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="member-fees">
                </div>
                <div class="col-md-6 col-sm-12" id="member-fines">
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-12" id="member-loans">
                </div>
                <div class="col-md-6 col-sm-12" id="member-refunds">
                </div>
              </div>
            </div>
          </div>
