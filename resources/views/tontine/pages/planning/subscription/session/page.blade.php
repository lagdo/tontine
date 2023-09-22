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
                        <a href="javascript:void(0)" class="pool-subscription-session-toggle">
                          @if($session->disabled($pool))<i class="fa fa-toggle-off"></i>@else<i class="fa fa-toggle-on"></i>@endif
                        </a>
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
{!! $pagination !!}
              </div>
