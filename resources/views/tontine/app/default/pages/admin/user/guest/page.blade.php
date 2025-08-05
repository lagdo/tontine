@php
  $inviteId = jq()->parent()->attr('data-invite-id')->toInt();
  $rqGuestUserFunc = rq(Ajax\User\Guest\GuestFunc::class);
  $rqGuestUserPage = rq(Ajax\User\Guest\GuestPage::class);
@endphp
                  <div class="table-responsive" id="content-guest-invites-page" @jxnEvent([
                    ['.btn-guest-invite-accept', 'click', $rqGuestUserFunc->accept($inviteId)
                      ->confirm(trans('tontine.invite.questions.accept'))],
                    ['.btn-guest-invite-refuse', 'click', $rqGuestUserFunc->refuse($inviteId)
                      ->confirm(trans('tontine.invite.questions.refuse'))],
                    ['.btn-guest-invite-delete', 'click', $rqGuestUserFunc->delete($inviteId)
                      ->confirm(trans('tontine.invite.questions.delete'))]])>

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
                          <td>{{ $invite->host->name }}<br/>{{ $invite->host->email }}</td>
                          <td>{{ $invite->status_label }}@if (($invite->active_label))<br/>{!! $invite->active_label !!}@endif</td>
                          <td class="table-item-menu">
@include('tontine::parts.table.menu', [
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
                    <nav @jxnPagination($rqGuestUserPage)>
                    </nav>
                  </div> <!-- End table -->
