@include('tontine.app.default.pages.meeting.session.menu.wrapper', ['session' => $session])

          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
              <div class="row sm-screen-selector mb-3" id="session-charges-sm-screens">
                <div class="col-12">
                  <div class="btn-group btn-group-sm btn-block" role="group">
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
          </div>
