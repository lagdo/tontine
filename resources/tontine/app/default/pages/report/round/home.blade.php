@inject('sqids', 'Sqids\SqidsInterface')
@php
  $sessionId = je('report-select-session')->rd()->select();
  $rqRound = rq(Ajax\App\Report\Round\Round::class);
  $rqRoundBalance = rq(Ajax\App\Report\Round\Balance::class);
  $rqRoundPool = rq(Ajax\App\Report\Round\Pool::class);
  $rqOptionsFunc = rq(Ajax\App\Guild\Options\OptionsFunc::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col-auto">
                <h2 class="section-title">{{ __('figures.titles.amounts') }}</h2>
              </div>
              <div class="col-auto">
                <div class="input-group float-left">
                  {{ $html->select('session_id', $sessions, $lastSession?->id ?? 0)
                    ->id('report-select-session')->class('form-control')
                    ->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqRound->select($sessionId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto ml-auto">
                <div class="btn-group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqOptionsFunc->editOptions())><i class="fa fa-cog"></i></button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-pdf"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.round',
                      ['roundId' => $sqids->encode([$round->id])]) }}">{{ __('tontine.report.actions.round') }}</a>
                  </div>
                </div>
                <div class="btn-group ml-3">
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-alt"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form',
                      ['form' => 'report']) }}">{{ __('meeting.entry.actions.report') }}</a>
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('entry.form',
                      ['form' => 'transactions']) }}">{{ __('meeting.entry.actions.transactions') }}</a>
                  </div>
                </div>
              </div>
            </div>
          </div>

@if (count($figures) > 0)
@foreach ($figures as $poolFigures)
@php
  $pool = $poolFigures['pool'];
  $stash->set('report.round.figures', $poolFigures);
@endphp
          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-auto">
                  <div class="section-title mt-0">{{ __('meeting.actions.pools') }} - {{ $pool->title }}</div>
                </div>
                <div class="col-auto ml-auto">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundPool
                      ->refresh($pool->id, $lastSession?->id ?? 0))><i class="fa fa-sync"></i></button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="table-responsive" @jxnBind($rqRoundPool, "pool-{$pool->id}")>
                    @jxnHtml($rqRoundPool)
                  </div>
                </div>
              </div>
            </div>
          </div>
@endforeach
@endif

          <div class="card shadow mb-4">
            <div class="card-body">
              <div class="row mb-2">
                <div class="col-auto">
                  <div class="section-title mt-0">{!! __('meeting.titles.amounts') !!}</div>
                </div>
                <div class="col-auto ml-auto">
                  <div class="btn-group" role="group">
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundBalance->select($lastSession?->id ?? 0))><i class="fa fa-sync"></i></button>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col">
                  <div class="table-responsive" @jxnBind($rqRoundBalance)>
                    @jxnHtml($rqRoundBalance)
                  </div>
                </div>
              </div>
            </div>
          </div>
