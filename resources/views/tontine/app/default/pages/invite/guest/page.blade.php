@php
  $inviteId = Jaxon\jq()->parent()->attr('data-invite-id')->toInt();
  $rqGuestInvite = Jaxon\rq(App\Ajax\Web\Tontine\Invite\Guest::class);
  $rqGuestInvitePage = Jaxon\rq(App\Ajax\Web\Tontine\Invite\GuestPage::class);
@endphp
                  <div class="table-responsive" id="content-guest-invites-page" @jxnTarget()>
                    <div @jxnOn(['.btn-guest-invite-accept', 'click', ''], $rqGuestInvite->accept($inviteId)
                      ->confirm(trans('tontine.invite.questions.accept')))></div>
                    <div @jxnOn(['.btn-guest-invite-refuse', 'click', ''], $rqGuestInvite->refuse($inviteId)
                      ->confirm(trans('tontine.invite.questions.refuse')))></div>
                    <div @jxnOn(['.btn-guest-invite-delete', 'click', ''], $rqGuestInvite->delete($inviteId)
                      ->confirm(trans('tontine.invite.questions.delete')))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('tontine.invite.labels.host') !!}</th>
                          <th>{!! __('common.labels.status') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($invites as $invite)
@php
  $actions = !$invite->is_pending ? [] : [[
    'class' => 'btn-guest-invite-accept',
    'text' => __('tontine.invite.actions.accept'),
  ],[
    'class' => 'btn-guest-invite-refuse',
    'text' => __('tontine.invite.actions.refuse'),
  ]];
@endphp
                        <tr>
                          <td>{{ $invite->host->name }}@if (($invite->active_label))<br/>{!! $invite->active_label !!}@endif</td>
                          <td>{{ $invite->status_label }}</td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-invite-id',
  'dataIdValue' => $invite->id,
  'menus' => [
    ...$actions, [
    'class' => 'btn-guest-invite-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqGuestInvitePage)>
                    </nav>
                  </div> <!-- End table -->
