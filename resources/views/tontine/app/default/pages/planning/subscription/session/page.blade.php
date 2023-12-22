              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.title') !!}</th>
                      <th>{!! __('common.labels.date') !!}</th>
                      <th class="table-menu">{{ $total }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}</td>
                      <td>{{ $session->date }}</td>
                      <td class="table-item-menu" data-session-id="{{ $session->id }}">
@if($session->disabled($pool))
                        <a href="javascript:void(0)" class="pool-subscription-session-enable"><i class="fa fa-toggle-off"></i></a>
@else
                        <a href="javascript:void(0)" class="pool-subscription-session-disable"><i class="fa fa-toggle-on"></i></a>
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
{!! $pagination !!}
              </div>
