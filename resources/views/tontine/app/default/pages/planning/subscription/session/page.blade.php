@php
  $sessionId = Jaxon\jq()->parent()->attr('data-session-id')->toInt();
  $rqSession = Jaxon\rq(App\Ajax\Web\Planning\Subscription\Session::class);
  $rqSessionPage = Jaxon\rq(App\Ajax\Web\Planning\Subscription\SessionPage::class);
@endphp
                  <div class="table-responsive" id="pool-subscription-sessions-page" @jxnTarget()>
                    <div @jxnOn(['.pool-subscription-session-enable', 'click', ''], $rqSession->enableSession($sessionId))></div>
                    <div @jxnOn(['.pool-subscription-session-disable', 'click', ''], $rqSession->disableSession($sessionId)
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
                            <a role="link" class="pool-subscription-session-enable"><i class="fa fa-toggle-off"></i></a>
@else
                            <a role="link" class="pool-subscription-session-disable"><i class="fa fa-toggle-on"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav @jxnPagination($rqSessionPage)>
                    </nav>
                  </div> <!-- End table -->
