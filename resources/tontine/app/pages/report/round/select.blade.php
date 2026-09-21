@php
  $sessionId = Jaxon\select('report-select-session');
  $rqRoundFunc = rq(Ajax\App\Report\Round\RoundFunc::class);
@endphp
              <div class="col-auto">
                <div class="input-group float-left">
                  {{ $html->select('session_id', $sessions, $lastSession?->id ?? 0)
                    ->id('report-select-session')->class('form-control')
                    ->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
@if ($content === 'tables')
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundFunc
                      ->showRoundTables($sessionId))><i class="fa fa-caret-right"></i></button>
@endif
@if ($content === 'graphs')
                    <button type="button" class="btn btn-primary" @jxnClick($rqRoundFunc
                      ->showRoundGraphs($sessionId))><i class="fa fa-caret-right"></i></button>
@endif
                </div>
              </div>
