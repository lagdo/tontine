              <div class="col-auto">
                <h2 class="section-title">{{ $reportTitle }}</h2>
              </div>
@if($member === null)
              <div class="col-auto pl-0 ml-auto">
@include('tontine_app::pages.report.session.action.exports', ['sessionId' => $session->id])
              </div>
              <div class="col-auto pl-0">
@include('tontine_app::pages.report.session.action.menu', ['sessionId' => $session->id])
              </div>
@endif
