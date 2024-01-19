              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.title') !!}</th>
                      <th>{!! __('common.labels.date') !!}</th>
                      <th>{!! __('tontine.session.labels.host') !!}</th>
                      <th class="table-menu"></th>
                      <th class="table-menu"></th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}<br/>{{ $statuses[$session->status] }}</td>
                      <td>{{ $session->date }}<br/>{{ $session->times }}</td>
                      <td>{{ $session->host ? $session->host->name : '' }}</td>
                      <td class="table-item-menu" data-session-id="{{ $session->id }}">
                        @if ($session->opened)<button type="button" class="btn btn-primary btn-session-resync"><i class="fa fa-redo"></i></button>@endif
                      </td>
                      <td class="table-item-menu" data-session-id="{{ $session->id }}">
                        <button type="button" class="btn btn-primary btn-session-show"><i class="fa fa-arrow-circle-right"></i></button>
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
              </div>
