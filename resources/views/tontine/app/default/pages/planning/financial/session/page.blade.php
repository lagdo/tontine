@php
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
  $rqSessionFunc = rq(Ajax\App\Planning\Financial\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Planning\Financial\SessionPage::class);
@endphp
                      <div class="table-responsive" id="content-planning-sessions-page" @jxnTarget()>
                        <div @jxnEvent(['.pool-session-enable', 'click'], $rqSessionFunc->enableSession($sessionId))></div>
                        <div @jxnEvent(['.pool-session-disable', 'click'], $rqSessionFunc->disableSession($sessionId)
                          ->confirm(__('tontine.session.questions.disable')))></div>

                        <form id="pool-session-form">
                          <table class="table table-bordered responsive">
                            <thead>
                              <tr>
                                <th>{!! __('common.labels.title') !!}</th>
                                <th>{!! __('common.labels.date') !!}</th>
                                <th class="table-menu">{!! __('tontine.pool_round.labels.start') !!}</th>
                                <th class="table-menu">{!! __('tontine.pool_round.labels.end') !!}</th>
                                <th class="table-menu">{!! __('tontine.pool_round.labels.active') !!}</th>
                              </tr>
                            </thead>
                            <tbody>
@foreach ($sessions as $session)
                              <tr>
                                <td>{{ $session->title }}</td>
                                <td>{{ $session->date }}</td>
                                <td class="table-item-menu">
                                  {!! $html->radio('start_session', $session->id === $startSessionId, $session->id) !!}
                                </td>
                                <td class="table-item-menu">
                                  {!! $html->radio('end_session', $session->id === $endSessionId, $session->id) !!}
                                </td>
                                <td class="table-item-menu" data-session-id="{{ $session->id }}">
@if(!$session->candidate)
                                  &nbsp;
@else
@if($session->disabled($pool))
                                  <a role="link" tabindex="0" class="pool-session-enable"><i class="fa fa-toggle-off"></i></a>
@else
                                  <a role="link" tabindex="0" class="pool-session-disable"><i class="fa fa-toggle-on"></i></a>
@endif
@endif
                                </td>
                              </tr>
@endforeach
                            </tbody>
                          </table>
                        </form>
                        <nav @jxnPagination($rqSessionPage)>
                        </nav>
                      </div> <!-- End table -->
