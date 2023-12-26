                      <div class="table-responsive">
                        <table class="table table-bordered">
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
                                {!! Form::radio($field . '_session', $session->id, $session->id === $sessionId) !!}
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
{!! $pagination !!}
                      </div>
