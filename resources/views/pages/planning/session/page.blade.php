              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.title') !!}</th>
                      <th>{!! __('common.labels.date') !!}</th>
                      <th>{!! __('tontine.session.labels.times') !!}</th>
                      <th>{!! __('tontine.session.labels.host') !!}</th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td>{{ $session->date }}</td>
                      <td>{{ $session->times }}</td>
                      <td>{{ $session->host ? $session->host->name : '' }}</td>
                      <td class="table-item-menu">
@include('parts.table.menu', [
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => $session->id,
  'menus' => [[
    'class' => 'btn-session-edit',
    'text' => __('common.actions.edit'),
  ],[
    'class' => 'btn-session-venue',
    'text' => __('tontine.session.actions.venue'),
  ]],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
{!! $pagination !!}
              </div>
