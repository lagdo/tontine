@if ($session->opened)
                <div class="btn-group float-right ml-1" role="group">
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
  'menus' => array_filter($menus, fn($item) => $item['class'] !== "btn-session-$currentSessionPage"),
])
                </div>
@endif
                <div class="btn-group float-right ml-1" role="group">
                  <button type="button" class="btn btn-primary" id="btn-session-back"><i class="fa fa-arrow-left"></i></button>
                  <button type="button" class="btn btn-primary" id="btn-session-refresh"><i class="fa fa-sync"></i></button>
                </div>
