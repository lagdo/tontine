@php
  $sessionId = Jaxon\jq()->parent()->attr('data-session-id')->toInt();
  $rqPresence = Jaxon\rq(Ajax\App\Meeting\Presence\Presence::class);
  $rqSession = Jaxon\rq(Ajax\App\Meeting\Presence\Session::class);
  $rqSessionPage = Jaxon\rq(Ajax\App\Meeting\Presence\SessionPage::class);
@endphp
                  <div class="table-responsive" id="content-page-sessions" @jxnTarget()>
                    <div @jxnEvent(['.btn-toggle-session-presence', 'click'], $rqSession->togglePresence($sessionId))></div>
                    <div @jxnEvent(['.btn-show-session-presences', 'click'], $rqPresence->selectSession($sessionId))></div>

                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! !$member ? __('common.labels.title') : __('tontine.titles.session') !!}</th>
                          <th class="table-item-toggle"></th>
                          <th class="table-item-menu"></th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($sessions as $session)
                        <tr>
                          <td>{{ $session->title }}<br/>{{ $statuses[$session->status] }}</td>
                          <td class="table-item-toggle">{{ $memberCount - ($session->absents_count ?? 0) }}/{{ $memberCount }}</td>
                          <td class="table-item-menu" data-session-id="{{ $session->id }}">
@if (!$member)
                            <button type="button" class="btn btn-primary btn-show-session-presences"><i class="fa fa-arrow-circle-right"></i></button>
@elseif ($session->opened)
                            <a role="link" class="btn-toggle-session-presence"><i class="fa fa-toggle-{{
                              $absences->has($session->id) ? 'off' : 'on' }}"></i></a>
@else
                            <i class="fa fa-toggle-{{ $absences->has($session->id) ? 'off' : 'on' }}"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSessionPage)>
                    </nav>
                  </div> <!-- End table -->
