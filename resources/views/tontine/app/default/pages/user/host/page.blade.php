@php
  $inviteId = jq()->parent()->attr('data-invite-id')->toInt();
  $rqHostUser = rq(Ajax\App\Admin\User\Host\Host::class);
  $rqHostUserPage = rq(Ajax\App\Admin\User\Host\HostPage::class);
  $rqHostAccess = rq(Ajax\App\Admin\User\Host\Access::class);
@endphp
                  <div class="table-responsive" id="content-host-invites-page" @jxnTarget()>
                    <div @jxnEvent(['.btn-host-invite-access', 'click'], $rqHostAccess->home($inviteId))></div>
                    <div @jxnEvent(['.btn-host-invite-cancel', 'click'], $rqHostUser->cancel($inviteId)
                      ->confirm(trans('tontine.invite.questions.cancel')))></div>
                    <div @jxnEvent(['.btn-host-invite-delete', 'click'], $rqHostUser->delete($inviteId)
                      ->confirm(trans('tontine.invite.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('tontine.invite.labels.guest') !!}</th>
                          <th>{!! __('common.labels.status') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($invites as $invite)
@php
  $actions = !$invite->is_pending ? [] : [[
    'class' => 'btn-host-invite-cancel',
    'text' => __('tontine.invite.actions.cancel'),
  ]];
  if($invite->is_accepted)
  {
    $actions[] = [
      'class' => 'btn-host-invite-access',
      'text' => __('tontine.invite.actions.access'),
    ];
  }
@endphp
                        <tr>
                          <td>{{ $invite->guest->name }}@if (($invite->active_label))<br/>{!! $invite->active_label !!}@endif</td>
                          <td>{{ $invite->status_label }}</td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-invite-id',
  'dataIdValue' => $invite->id,
  'menus' => [
    ...$actions, [
    'class' => 'btn-host-invite-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqHostUserPage)>
                    </nav>
                  </div> <!-- End table -->
