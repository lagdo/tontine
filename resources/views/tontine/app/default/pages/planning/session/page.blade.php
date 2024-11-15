@php
  $sessionId = Jaxon\jq()->parent()->attr('data-session-id')->toInt();
  $rqSession = Jaxon\rq(Ajax\App\Planning\Session\Session::class);
  $rqSessionPage = Jaxon\rq(Ajax\App\Planning\Session\SessionPage::class);
@endphp
                    <div class="table-responsive" id="content-page-sessions" @jxnTarget()>
                      <div @jxnEvent(['.btn-session-edit', 'click'], $rqSession->edit($sessionId))></div>
                      <div @jxnEvent(['.btn-session-venue', 'click'], $rqSession->editVenue($sessionId))></div>
                      <div @jxnEvent(['.btn-session-delete', 'click'], $rqSession->delete($sessionId)
                        ->confirm(__('tontine.session.questions.delete')))></div>

                      <table class="table table-bordered responsive">
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
                      <nav @jxnPagination($rqSessionPage)>
                      </nav>
                    </div> <!-- End table -->
