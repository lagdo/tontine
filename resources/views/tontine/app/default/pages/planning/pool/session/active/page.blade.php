@php
  $sessionId = jq()->parent()->attr('data-session-id')->toInt();
  $rqSessionFunc = rq(Ajax\App\Planning\Pool\Session\SessionFunc::class);
  $rqSessionPage = rq(Ajax\App\Planning\Pool\Session\SessionPage::class);
@endphp
                      <div class="table-responsive" id="content-planning-active-sessions-page" @jxnTarget()>
                        <div @jxnEvent(['.pool-subscription-session-enable', 'click'], $rqSessionFunc->enableSession($sessionId))></div>
                        <div @jxnEvent(['.pool-subscription-session-disable', 'click'], $rqSessionFunc->disableSession($sessionId)
                          ->confirm(__('tontine.session.questions.disable')))></div>

                        <table class="table table-bordered responsive">
                          <thead>
                            <tr>
                              <th>{!! __('common.labels.title') !!}</th>
                              <th>{!! __('common.labels.date') !!}</th>
                              <th class="table-menu">&nbsp;</th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($sessions as $session)
                            <tr>
                              <td>{{ $session->title }}</td>
                              <td>{{ $session->date }}</td>
                              <td class="table-item-menu" data-session-id="{{ $session->id }}">
@if($session->disabled($pool))
                                <a role="link" tabindex="0" class="pool-subscription-session-enable"><i class="fa fa-toggle-off"></i></a>
@else
                                <a role="link" tabindex="0" class="pool-subscription-session-disable"><i class="fa fa-toggle-on"></i></a>
@endif
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
                        <nav @jxnPagination($rqSessionPage)>
                        </nav>
                      </div> <!-- End table -->
