@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqAction = rq(Ajax\App\Report\Round\Action::class);
  $rqSelect = rq(Ajax\App\Report\Round\Select::class);
  $rqRoundTables = rq(Ajax\App\Report\Round\RoundTables::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('figures.titles.amounts') }}</h2>
              </div>
              <div class="col-auto ml-auto">
@include('tontine_app::pages.report.round.menu.export', [
  'roundId' => $sqids->encode([$round->id]),
  'guildId' => $sqids->encode([$currentGuild->id]),
])
              </div>
            </div>
            <div class="row mb-2" @jxnBind($rqSelect)>
            </div>
          </div>

          <div @jxnBind($rqRoundTables)>
          </div>
