@php
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
  $rqSessionFunc = rq(Ajax\App\Guild\Calendar\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Guild\Calendar\SessionPage::class);
@endphp
                    <div class="table-responsive" id="content-planning-sessions-page" @jxnTarget()>
                      <div @jxnEvent(['.btn-session-edit', 'click'], $rqSessionFunc->edit($sessionId))></div>
                      <div @jxnEvent(['.btn-session-venue', 'click'], $rqSessionFunc->editVenue($sessionId))></div>
                      <div @jxnEvent(['.btn-session-delete', 'click'], $rqSessionFunc->delete($sessionId)
                        ->confirm(__('tontine.session.questions.delete')))></div>

                      <table class="table table-bordered responsive">
                        <thead>
                          <tr>
                            <th>{!! __('common.labels.title') !!}</th>
                            <th>{!! __('common.labels.status') !!}</th>
                            <th>{!! __('common.labels.date') !!}</th>
                            <th>{!! __('tontine.session.labels.times') !!}</th>
                            <th class="table-menu"></th>
                          </tr>
                        </thead>
                        <tbody>
@foreach ($sessions as $session)
                          <tr>
                            <td>{{ $session->title }}</td>
                            <td>{{ $statuses[$session->status] }}</td>
                            <td>{{ $session->date('day_date') }}</td>
                            <td>{{ $session->times }}</td>
                            <td class="table-item-menu">
@include('tontine::parts.table.menu', [
  'dataIdKey' => 'data-session-id',
  'dataIdValue' => $session->id,
  'menus' => [[
    'class' => 'btn-session-venue',
    'text' => __('tontine.session.actions.venue'),
  ],
  null,[
    'class' => 'btn-session-edit',
    'text' => __('common.actions.edit'),
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
                      <nav @jxnPagination($rqSessionPage)>
                      </nav>
                    </div> <!-- End table -->
