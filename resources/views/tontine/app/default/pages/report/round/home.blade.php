@inject('sqids', 'Sqids\SqidsInterface')
@php
  $rqRound = rq(Ajax\App\Report\Round\Round::class);
  $rqRoundBalance = rq(Ajax\App\Report\Round\Balance::class);
  $rqRoundPool = rq(Ajax\App\Report\Round\Pool::class);
  $rqOptionsFunc = rq(Ajax\App\Guild\Options\OptionsFunc::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title">{{ __('figures.titles.amounts') }}</h2>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-1" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqRound->home())><i class="fa fa-sync"></i></button>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-1">
                  <button type="button" class="btn btn-primary" @jxnClick($rqOptionsFunc->editOptions())><i class="fa fa-cog"></i></button>
                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fa fa-file-pdf"></i>
                  </button>
                  <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" target="_blank" href="{{ $locale->route('report.round',
                      ['roundId' => $sqids->encode([$round->id])]) }}">{{ __('tontine.report.actions.round') }}</a>
                  </div>
                </div>
              </div>
              <div class="col-auto">
                <div class="btn-group float-right ml-1">
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
                <div class="col">
                  <div class="section-title mt-0">{{ __('meeting.actions.pools') }} - {{ $pool->title }}</div>
                </div>
                <div class="col-auto">
                  <div class="btn-group float-right ml-1" role="group">
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundPool->refresh($pool->id))><i class="fa fa-sync"></i></button>
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
                <div class="col">
                  <div class="section-title mt-0">{!! __('meeting.titles.amounts') !!}</div>
                </div>
                <div class="col-auto">
                  <div class="btn-group float-right ml-1" role="group">
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundBalance->render())><i class="fa fa-sync"></i></button>
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
