@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row sm-screen-selector mb-3" id="session-savings-sm-screens">
                <div class="col-12">
                  <div class="btn-group btn-group-sm btn-block" role="group">
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
          </div>
