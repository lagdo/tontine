@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqRoundFunc = rq(Ajax\App\Report\Round\RoundFunc::class);
@endphp
              <div class="col">
                <h2 class="section-title">{{ __('figures.titles.amounts') }}</h2>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group ml-1">
@if($content === 'tables')
                  <button type="button" class="btn btn-primary" @jxnClick($rqRoundFunc->showGraphs())>
                    <i class="fa fa-chart-pie"></i>
                  </button>
@endif
@if($content === 'graphs')
                  <button type="button" class="btn btn-primary" @jxnClick($rqRoundFunc->showTables())>
                    <i class="fa fa-table"></i>
                  </button>
@endif
                </div>
              </div>
              <div class="col-auto ml-auto">
@include('tontine_app::pages.report.round.header.export', [
  'roundId' => $sqids->encode([$round->id]),
  'guildId' => $sqids->encode([$currentGuild->id]),
])
              </div>
