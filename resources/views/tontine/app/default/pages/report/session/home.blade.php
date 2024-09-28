@php
  $sessionId = Jaxon\pm()->select('select-session')->toInt();
  $memberId = Jaxon\pm()->select('select-member')->toInt();
  $rqSession = Jaxon\rq(App\Ajax\Web\Report\Session::class);
  $rqActionExport = Jaxon\rq(App\Ajax\Web\Report\Session\Action\Export::class);
  $rqActionMenu = Jaxon\rq(App\Ajax\Web\Report\Session\Action\Menu::class);
@endphp
          <div class="section-body">
            <div class="row">
              <div class="col">
                <h2 class="section-title" id="session-report-title"></h2>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ $htmlBuilder->select('session_id', $sessions, 0)->id('select-session')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showSession($sessionId))><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto" @jxnShow($rqActionExport)>
              </div>
              <div class="col-auto">
                <div class="input-group">
                  {{ $htmlBuilder->select('member_id', $members, 0)->id('select-member')
                    ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') }}
                  <div class="input-group-append">
                    <button type="button" class="btn btn-primary" @jxnClick($rqSession->showMember($sessionId, $memberId))><i class="fa fa-arrow-right"></i></button>
                  </div>
                </div>
              </div>
              <div class="col-auto" @jxnShow($rqActionMenu)>
              </div>
            </div>
          </div>

          <!-- Data tables -->
          <div class="card shadow mb-4">
            <div class="card-body" id="content-page">
            </div>
          </div>
