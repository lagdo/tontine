                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>{!! __('common.labels.title') !!}</th>
                              <th>{!! __('common.labels.date') !!}</th>
                              <th class="table-menu"></th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($sessions as $session)
                            <tr>
                              <td>{{ $session->title }}<br/>{{ $statuses[$session->status] }}</td>
                              <td>{{ $session->date }}<br/>{{ $session->times }}</td>
                              <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => $session->id,
  'menus' => [[
    'class' => 'btn-session-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-session-venue',
    'text' => __('tontine.session.actions.venue'),
  ],[
    'class' => 'btn-session-delete',
    'text' => __('common.actions.delete'),
  ]],
])
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
{!! $pagination !!}
