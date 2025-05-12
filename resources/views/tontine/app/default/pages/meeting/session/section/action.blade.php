@php
  $rqSession = rq(Ajax\App\Meeting\Session\Session::class);
  $rqSectionFunc = rq(Ajax\App\Meeting\Session\SectionFunc::class);
@endphp
              <div class="col-auto">
                <h2 class="section-title">{{ $session->title }}: {!! $sectionTitle !!}</h2>
              </div>
              <div class="col-auto ml-auto">
@include('tontine::pages.report.session.action.exports', ['sessionId' => $session->id])
                <div class="btn-group ml-2" role="group" @jxnTarget()>
                  <div @jxnEvent(['.btn-session-pools', 'click'], $rqSectionFunc->pools())></div>
                  <div @jxnEvent(['.btn-session-charges', 'click'], $rqSectionFunc->charges())></div>
                  <div @jxnEvent(['.btn-session-savings', 'click'], $rqSectionFunc->savings())></div>
                  <div @jxnEvent(['.btn-session-refunds', 'click'], $rqSectionFunc->refunds())></div>
                  <div @jxnEvent(['.btn-session-profits', 'click'], $rqSectionFunc->profits())></div>
                  <div @jxnEvent(['.btn-session-outflows', 'click'], $rqSectionFunc->outflows())></div>
                  <div @jxnEvent(['.btn-session-reports', 'click'], $rqSectionFunc->reports())></div>

@if($session->opened)
@php
  $menus = [[
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
  ],[
    'class' => 'btn-session-reports',
    'text' => __('meeting.actions.reports'),
  ]];
@endphp
@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'btnIcon' => 'fa-stream',
  'menus' => array_filter($menus, fn($item) => $item['class'] !== "btn-session-$section"),
])
@endif
                </div>
                <div class="btn-group ml-2" role="group" @jxnTarget()>
                  <div @jxnEvent(['.btn-session-prev', 'click'], $rqSectionFunc->$section($prevSession?->id ?? 0))></div>
                  <div @jxnEvent(['.btn-session-next', 'click'], $rqSectionFunc->$section($nextSession?->id ?? 0))></div>

@php
  $menus = [];
  if($prevSession !== null)
  {
    $menus[] = [
      'class' => 'btn-session-prev',
      'text' => __('meeting.session.actions.prev'),
    ];
  }
  if($nextSession !== null)
  {
    $menus[] = [
      'class' => 'btn-session-next',
      'text' => __('meeting.session.actions.next'),
    ];
  }
@endphp
@if(count($menus) > 0)
@include('tontine::parts.table.menu', [
  'btnSize' => '',
  'btnIcon' => 'fa-sort fa-rotate-90',
  'menus' => $menus,
])
@endif
                </div>
                <div class="btn-group ml-3" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->home())><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSectionFunc->$section())><i class="fa fa-sync"></i></button>
                </div>
              </div>
