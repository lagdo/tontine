@php
  $rqRoundBalance = rq(Ajax\App\Report\Round\Table\Balance::class);
  $rqRoundPool = rq(Ajax\App\Report\Round\Table\Pool::class);
@endphp
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
                      <button type="button" class="btn btn-primary" @jxnClick($rqRoundPool->refresh($pool->id, $lastSession?->id ?? 0))>
                        <i class="fa fa-sync"></i>
                      </button>
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
                      <button type="button" class="btn btn-primary" @jxnClick($rqRoundBalance->select($lastSession?->id ?? 0))>
                        <i class="fa fa-sync"></i>
                      </button>
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
