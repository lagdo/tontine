@php
  $sessionId = pm()->select('report-select-session')->toInt();
  $memberId = pm()->select('report-select-member')->toInt();
  $rqSession = rq(Ajax\App\Report\Session\Session::class);
  $rqSessionContent = rq(Ajax\App\Report\Session\SessionContent::class);
  $rqReportTitle = rq(Ajax\App\Report\Session\ReportTitle::class);
  $rqActionExport = rq(Ajax\App\Report\Session\Action\Export::class);
  $rqActionMenu = rq(Ajax\App\Report\Session\Action\Menu::class);
@endphp
          <div class="section-body">
            <div class="row mb-2">
              <div class="col">
                <h2 class="section-title" @jxnBind($rqReportTitle)></h2>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ $html->select('session_id', $sessions, 0)->id('report-select-session')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showSession($sessionId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto" @jxnBind($rqActionExport)>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ $html->select('member_id', $members, 0)->id('report-select-member')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showMember($sessionId, $memberId))><i class="fa fa-caret-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto" @jxnBind($rqActionMenu)>
              </div>
            </div>
          </div>

          <div @jxnBind($rqSessionContent)>
          </div>
