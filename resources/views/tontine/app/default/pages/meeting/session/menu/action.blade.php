@php
  $rqSession = Jaxon\rq(App\Ajax\Web\Meeting\Session\Session::class);
  $rqSection = Jaxon\rq(App\Ajax\Web\Meeting\Session\Section::class);
@endphp
                <div class="btn-group float-right ml-1" role="group"  @jxnTarget()>
                  <div @jxnOn(['.btn-session-pools', 'click', ''], $rqSection->pools())></div>
                  <div @jxnOn(['.btn-session-charges', 'click', ''], $rqSection->charges())></div>
                  <div @jxnOn(['.btn-session-savings', 'click', ''], $rqSection->savings())></div>
                  <div @jxnOn(['.btn-session-credits', 'click', ''], $rqSection->credits())></div>
                  <div @jxnOn(['.btn-session-cash', 'click', ''], $rqSection->cash())></div>
                  <div @jxnOn(['.btn-session-reports', 'click', ''], $rqSection->reports())></div>

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
                  <button type="button" class="btn btn-primary" @jxnClick($rqSection->$section())><i class="fa fa-sync"></i></button>
                </div>
