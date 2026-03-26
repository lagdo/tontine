@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqSessionFunc = rq(Ajax\App\Report\Session\SessionFunc::class);
@endphp
              <div class="col">
                <h2 class="section-title">{{ $title }}</h2>
              </div>
@if($member === null)
              <div class="col-auto ml-auto">
                <div class="btn-group ml-1">
@if($content === 'tables')
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->showGraphs())>
                    <i class="fa fa-chart-pie"></i>
                  </button>
@endif
@if($content === 'graphs')
                  <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc->showTables())>
                    <i class="fa fa-table"></i>
                  </button>
@endif
                </div>
              </div>
              <div class="col-auto ml-auto">
@include('tontine_app::pages.report.session.header.export', [
  'guildId' => $sqids->encode([$currentGuild->id]),
  'sessionId' => $sqids->encode([$session->id]),
])
              </div>
@endif
