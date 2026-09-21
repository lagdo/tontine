@php
  $sessionId = Jaxon\select('report-select-session')->toInt();
  $memberId = Jaxon\select('report-select-member')->toInt();
  $rqSessionFunc = rq(Ajax\App\Report\Session\SessionFunc::class);
@endphp
              <div class="col-auto pr-0 mr-0">
                <div class="input-group float-left">
                  {{ $html->select('session_id', $sessions, 0)->id('report-select-session')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
@if ($content === 'tables')
                    <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc
                      ->showSessionTables($sessionId))><i class="fa fa-caret-right"></i></button>
@endif
@if ($content === 'graphs')
                    <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc
                      ->showSessionGraphs($sessionId))><i class="fa fa-caret-right"></i></button>
@endif
                  </div>
                </div>
              </div>
@if ($content === 'tables')
              <div class="col-auto pr-0 mr-0">
                <div class="input-group float-left">
                  {{ $html->select('member_id', $members, 0)->id('report-select-member')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSessionFunc
                      ->showMemberTables($sessionId, $memberId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
@endif
