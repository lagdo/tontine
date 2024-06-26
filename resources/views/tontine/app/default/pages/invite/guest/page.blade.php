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
                    <nav>{!! $pagination !!}</nav>
