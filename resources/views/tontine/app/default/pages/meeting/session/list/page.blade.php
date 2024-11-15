@php
  $rqSession = Jaxon\rq(Ajax\App\Meeting\Session\Session::class);
  $rqSessionPage = Jaxon\rq(Ajax\App\Meeting\Session\SessionPage::class);
  $rqSection = Jaxon\rq(Ajax\App\Meeting\Session\Section::class);
  $rqSummary = Jaxon\rq(Ajax\App\Meeting\Summary\Summary::class);
  $sessionId = Jaxon\jq()->parent()->attr('data-session-id')->toInt();
@endphp
              <div class="table-responsive" id="content-page" @jxnTarget()>
                <div @jxnOn(['.btn-session-open', 'click', ''], $rqSession->open($sessionId)
                  ->confirm(__('tontine.session.questions.open') . '<br/>' .
                    __('tontine.session.questions.warning')))></div>
                <div @jxnOn(['.btn-session-close', 'click', ''], $rqSession->close($sessionId)
                  ->confirm(__('tontine.session.questions.close')))></div>
                <div @jxnOn(['.btn-session-summary', 'click', ''], $rqSummary->home($sessionId))></div>
                <div @jxnOn(['.btn-session-pools', 'click', ''], $rqSection->pools($sessionId))></div>
                <div @jxnOn(['.btn-session-savings', 'click', ''], $rqSection->savings($sessionId))></div>
                <div @jxnOn(['.btn-session-credits', 'click', ''], $rqSection->credits($sessionId))></div>
                <div @jxnOn(['.btn-session-cash', 'click', ''], $rqSection->cash($sessionId))></div>
                <div @jxnOn(['.btn-session-charges', 'click', ''], $rqSection->charges($sessionId))></div>
                <div @jxnOn(['.btn-session-reports', 'click', ''], $rqSection->reports($sessionId))></div>

                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.title') !!}</th>
                      <th>{!! __('common.labels.date') !!}</th>
                      <th>{!! __('tontine.session.labels.host') !!}</th>
                      <th class="table-menu"></th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}<br/>{{ $statuses[$session->status] }}</td>
                      <td>{{ $session->date }}<br/>{{ $session->times }}</td>
                      <td>{{ $session->host ? $session->host->name : '' }}</td>
                      <td class="table-item-menu">
                        <div class="btn-group btn-group-sm float-right" data-session-id="{{ $session->id }}" role="group">
@if ($session->opened)
                          <button type="button" class="btn btn-primary btn-session-close"><i class="fa fa-lock-open"></i></button>
@elseif($session->pending || $session->closed)
                          <button type="button" class="btn btn-primary btn-session-open"><i class="fa fa-lock"></i></button>
@endif
                        </div>
                      </td>
                      <td class="table-item-menu">
@php
  $openedSessionItems = !$session->opened ? [] : [[
    'class' => 'btn-session-pools',
    'text' => __('meeting.actions.pools'),
  ],[
    'class' => 'btn-session-charges',
    'text' => __('meeting.actions.charges'),
  ],[
    'class' => 'btn-session-savings',
    'text' => __('meeting.actions.savings'),
  ],[
    'class' => 'btn-session-credits',
    'text' => __('meeting.actions.credits'),
  ],[
    'class' => 'btn-session-cash',
    'text' => __('meeting.actions.cash'),
  ]];
@endphp
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => $session->id,
  'menus' => [[
    'class' => 'btn-session-summary',
    'text' => __('meeting.actions.summary'),
  ],
  ...$openedSessionItems,
  [
    'class' => 'btn-session-reports',
    'text' => __('meeting.actions.reports'),
  ]],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav @jxnPagination($rqSessionPage)>
                </nav>
              </div>
