              <div class="table-responsive">
                <table class="table table-bordered responsive">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.title') !!}</th>
                      <th>{!! __('common.labels.date') !!}</th>
                      <th>{!! __('tontine.session.labels.host') !!}</th>
                      <th class="table-menu"></th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}<br/>{{ $statuses[$session->status] }}</td>
                      <td>{{ $session->date }}<br/>{{ $session->times }}</td>
                      <td>{{ $session->host ? $session->host->name : '' }}</td>
                      <td class="table-item-menu">
                        <div class="btn-group btn-group-sm float-right" data-session-id="{{ $session->id }}" role="group">
@if ($session->opened)
                          <button type="button" class="btn btn-primary btn-session-resync"><i class="fa fa-redo"></i></button>
                          <button type="button" class="btn btn-primary btn-session-close"><i class="fa fa-lock-open"></i></button>
@elseif($session->pending || $session->closed)
                          <button type="button" class="btn btn-primary btn-session-open"><i class="fa fa-lock"></i></button>
@endif
                        </div>
                      </td>
                      <td class="table-item-menu">
@include('tontine.app.default.parts.table.menu', [
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => $session->id,
  'menus' => [[
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
  ]],
])
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
