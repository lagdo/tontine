          <div class="section-body">
            <div class="row">
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}: {!! __("meeting.actions.$currentSessionPage") !!}</h2>
              </div>
              <div class="col">
@include('tontine.app.default.pages.report.session.action.exports', ['sessionId' => $session->id])
@include('tontine.app.default.pages.meeting.session.menu.action')
              </div>
            </div>
          </div>
