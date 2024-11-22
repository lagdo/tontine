@php
  $rqStartSessionPage = Jaxon\rq(Ajax\App\Planning\Pool\Session\Pool\StartSessionPage::class);
@endphp
                      <div class="table-responsive">
                        <form id="start-session-form">
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
                                <td class="table-item-menu">
                                  {!! $htmlBuilder->radio('start_session', $session->id === $sessionId, $session->id) !!}
                                </td>
                              </tr>
@endforeach
                            </tbody>
                          </table>
                        </form>
                        <nav @jxnPagination($rqStartSessionPage)>
                        </nav>
                      </div> <!-- End table -->
