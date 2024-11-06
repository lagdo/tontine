                      <!-- Data tables -->
                      <div class="table-responsive">
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
                                {!! $htmlBuilder->radio($field . '_session', $session->id === $sessionId, $session->id) !!}
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
                        <nav @jxnPagination($rqPoolRoundSession)>
                        </nav>
                      </div> <!-- End table -->
