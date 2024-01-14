                        <table class="table table-bordered">
                          <thead>
                            <tr>
                              <th>{!! __('common.labels.title') !!}</th>
                              <th class="table-item-menu"></th>
                            </tr>
                          </thead>
                          <tbody>
@foreach ($sessions as $session)
                            <tr>
                              <td>{{ $session->title }}</td>
                              <td class="table-item-menu" data-session-id="{{ $session->id }}">
                                <button type="button" class="btn btn-primary btn-show-session-presence"><i class="fa fa-arrow-circle-right"></i></button>
                              </td>
                            </tr>
@endforeach
                          </tbody>
                        </table>
{!! $pagination !!}
