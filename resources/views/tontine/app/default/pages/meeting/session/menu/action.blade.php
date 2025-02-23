@php
  $rqSession = rq(Ajax\App\Meeting\Session\Session::class);
  $rqSectionFunc = rq(Ajax\App\Meeting\Session\SectionFunc::class);
@endphp
                <div class="btn-group float-right ml-1" role="group"  @jxnTarget()>
                  <div @jxnEvent(['.btn-session-pools', 'click'], $rqSectionFunc->pools())></div>
                  <div @jxnEvent(['.btn-session-charges', 'click'], $rqSectionFunc->charges())></div>
                  <div @jxnEvent(['.btn-session-savings', 'click'], $rqSectionFunc->savings())></div>
                  <div @jxnEvent(['.btn-session-credits', 'click'], $rqSectionFunc->credits())></div>
                  <div @jxnEvent(['.btn-session-refunds', 'click'], $rqSectionFunc->refunds())></div>
                  <div @jxnEvent(['.btn-session-cash', 'click'], $rqSectionFunc->cash())></div>
                  <div @jxnEvent(['.btn-session-reports', 'click'], $rqSectionFunc->reports())></div>

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
    'class' => 'btn-session-credits',
    'text' => __('meeting.actions.credits'),
  ],[
    'class' => 'btn-session-refunds',
    'text' => __('meeting.actions.refunds'),
  ],[
    'class' => 'btn-session-cash',
    'text' => __('meeting.actions.cash'),
  ],[
    'class' => 'btn-session-reports',
    'text' => __('meeting.actions.reports'),
  ]];
@endphp
@include('tontine.app.default.parts.table.menu', [
  'btnSize' => '',
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => 0,
  'menus' => array_filter($menus, fn($item) => $item['class'] !== "btn-session-$section"),
])
                </div>
                <div class="btn-group float-right ml-1" role="group">
                  <button type="button" class="btn btn-primary" @jxnClick($rqSession->home())><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" @jxnClick($rqSectionFunc->$section())><i class="fa fa-sync"></i></button>
                </div>
