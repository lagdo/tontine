@php
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Session\SessionPage::class);
  $rqSectionFunc = rq(Ajax\App\Meeting\Session\SectionFunc::class);
  $rqSummary = rq(Ajax\App\Meeting\Summary\Summary::class);
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
@endphp
              <div class="table-responsive" id="content-sessions-page" @jxnTarget()>
                <div @jxnEvent(['.btn-session-open', 'click'], $rqSessionFunc->open($sessionId)
                  ->confirm(__('tontine.session.questions.open') . '<br/>' .
                    __('tontine.session.questions.warning')))></div>
                <div @jxnEvent(['.btn-session-close', 'click'], $rqSessionFunc->close($sessionId)
                  ->confirm(__('tontine.session.questions.close')))></div>
                <div @jxnEvent(['.btn-session-summary', 'click'], $rqSummary->home($sessionId))></div>
                <div @jxnEvent(['.btn-session-pools', 'click'], $rqSectionFunc->pools($sessionId))></div>
                <div @jxnEvent(['.btn-session-savings', 'click'], $rqSectionFunc->savings($sessionId))></div>
                <div @jxnEvent(['.btn-session-credits', 'click'], $rqSectionFunc->credits($sessionId))></div>
                <div @jxnEvent(['.btn-session-refunds', 'click'], $rqSectionFunc->refunds($sessionId))></div>
                <div @jxnEvent(['.btn-session-cash', 'click'], $rqSectionFunc->cash($sessionId))></div>
                <div @jxnEvent(['.btn-session-charges', 'click'], $rqSectionFunc->charges($sessionId))></div>
                <div @jxnEvent(['.btn-session-reports', 'click'], $rqSectionFunc->reports($sessionId))></div>

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
  $openedSessionItems = !$session->opened ? [] : [
    null, [
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
      'class' => 'btn-session-refunds',
      'text' => __('meeting.actions.refunds'),
    ],[
      'class' => 'btn-session-cash',
      'text' => __('meeting.actions.cash'),
    ],
    null,
  ];
@endphp
@include('tontine::parts.table.menu', [
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
