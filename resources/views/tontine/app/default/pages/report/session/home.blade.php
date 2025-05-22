@php
  $sessionId = pm()->select('report-select-session')->toInt();
  $memberId = pm()->select('report-select-member')->toInt();
  $rqSession = rq(Ajax\App\Report\Session\Session::class);
  $rqSessionContent = rq(Ajax\App\Report\Session\SessionContent::class);
  $rqReportHeader = rq(Ajax\App\Report\Session\ReportHeader::class);
@endphp
          <div class="section-body">
            <div class="row mb-2" @jxnBind($rqReportHeader)>
            </div>
            <div class="row">
              <div class="col-auto mb-2 pr-0 mr-0">
                <div class="input-group float-left">
                  {{ $html->select('session_id', $sessions, 0)->id('report-select-session')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showSession($sessionId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto mb-2 pr-0 mr-0">
                <div class="input-group float-left">
                  {{ $html->select('member_id', $members, 0)->id('report-select-member')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 5px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showMember($sessionId, $memberId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div @jxnBind($rqSessionContent)>
          </div>
