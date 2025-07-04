@php
  $rqSessionFunc = rq(Ajax\App\Meeting\Session\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Meeting\Session\SessionPage::class);
  $rqSectionFunc = rq(Ajax\App\Meeting\Session\SectionFunc::class);
  $rqSummary = rq(Ajax\App\Meeting\Summary\Summary::class);
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
@endphp
              <div class="table-responsive" id="content-sessions-page" @jxnEvent([
                ['.btn-session-open', 'click', $rqSessionFunc->open($sessionId)
                  ->confirm(__('tontine.session.questions.open') . '<br/>' .
                    __('tontine.session.questions.warning'))],
                ['.btn-session-close', 'click', $rqSessionFunc->close($sessionId)
                  ->confirm(__('tontine.session.questions.close'))],
                ['.btn-session-summary', 'click', $rqSummary->home($sessionId)],
                ['.btn-session-pools', 'click', $rqSectionFunc->pools($sessionId)],
                ['.btn-session-charges', 'click', $rqSectionFunc->charges($sessionId)],
                ['.btn-session-savings', 'click', $rqSectionFunc->savings($sessionId)],
                ['.btn-session-refunds', 'click', $rqSectionFunc->refunds($sessionId)],
                ['.btn-session-profits', 'click', $rqSectionFunc->profits($sessionId)],
                ['.btn-session-outflows', 'click', $rqSectionFunc->outflows($sessionId)],
                ['.btn-session-reports', 'click', $rqSectionFunc->reports($sessionId)]])>

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
                      <td>{{ $session->date('day_date') }}<br/>{{ $session->times }}</td>
                      <td>{{ $session->host?->name ?? '' }}</td>
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
    [
      'class' => 'btn-session-pools',
      'text' => __('meeting.actions.pools'),
    ],[
      'class' => 'btn-session-charges',
      'text' => __('meeting.actions.charges'),
    ],[
      'class' => 'btn-session-savings',
      'text' => __('meeting.actions.savings'),
    ],[
      'class' => 'btn-session-refunds',
      'text' => __('meeting.actions.refunds'),
    ],[
      'class' => 'btn-session-profits',
      'text' => __('meeting.actions.profits'),
    ],[
      'class' => 'btn-session-outflows',
      'text' => __('meeting.actions.outflows'),
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
  ], null,
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
