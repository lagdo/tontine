                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.active') !!}</th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($invites as $invite)
                        <tr>
                          <td>{{ $invite->guest->name }}</td>
                          <td>{{ $invite->status_label }}</td>
                          <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-invite-id',
  'dataIdValue' => $invite->id,
  'menus' => [[
    'class' => 'btn-host-invite-access',
    'text' => __('tontine.invite.actions.access'),
  ],[
    'class' => 'btn-host-invite-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
