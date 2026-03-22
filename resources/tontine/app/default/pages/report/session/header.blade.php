@php
  $rqSessionFunc = rq(Ajax\App\Report\Session\SessionFunc::class);
@endphp
              <div class="col-auto">
                <h2 class="section-title">{{ $title }}</h2>
              </div>
@if($member === null)
@if($content === 'tables')
              <div class="col-auto ml-auto">
                <div class="btn-group ml-1">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->showGraphs())><i class="fa fa-chart-pie"></i></button>
                </div>
              </div>
@endif
@if($content === 'graphs')
              <div class="col-auto ml-auto">
                <div class="btn-group ml-1">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->showTables())><i class="fa fa-table"></i></button>
                </div>
              </div>
@endif
              <div class="col-auto pl-0">
@include('tontine_app::pages.report.session.action.exports', ['sessionId' => $session->id])
              </div>
              <div class="col-auto pl-0">
@include('tontine_app::pages.report.session.action.menu', ['sessionId' => $session->id])
              </div>
@endif
