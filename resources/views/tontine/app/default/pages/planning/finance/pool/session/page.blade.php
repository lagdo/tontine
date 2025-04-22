@php
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
  $rqSessionFunc = rq(Ajax\App\Planning\Finance\Pool\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Planning\Finance\Pool\SessionPage::class);
  $poolSessionIds = $pool->sessions->pluck('id', 'id');
  $poolDisabledSessionIds = $pool->disabled_sessions->pluck('id', 'id');
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
                                <th class="table-menu">{!! __('tontine.session.labels.start') !!}</th>
                                <th class="table-menu">{!! __('tontine.session.labels.end') !!}</th>
                                <th class="table-menu">{!! __('tontine.session.labels.active') !!}</th>
                              </tr>
                            </thead>
                            <tbody>
@foreach ($sessions as $session)
                              <tr>
                                <td>{{ $session->title }}<br/>{{ $session->date }}</td>
                                <td class="table-item-menu">
                                  {!! $html->radio('start_sid', $session->id === $pool->start_sid, $session->id) !!}
                                </td>
                                <td class="table-item-menu">
                                  {!! $html->radio('end_sid', $session->id === $pool->end_sid, $session->id) !!}
                                </td>
                                <td class="table-item-menu" data-session-id="{{ $session->id }}">
@if($poolDisabledSessionIds->has($session->id))
                                  <a role="link" tabindex="0" class="pool-session-enable"><i class="fa fa-toggle-off"></i></a>
@elseif($poolSessionIds->has($session->id))
                                  <a role="link" tabindex="0" class="pool-session-disable"><i class="fa fa-toggle-on"></i></a>
@else
                                  &nbsp;
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
