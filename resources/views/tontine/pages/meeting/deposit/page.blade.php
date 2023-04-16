                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th>{!! __('common.labels.paid') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($receivables as $receivable)
                    <tr>
                      <td>{{ $receivable->subscription->member->name }}</td>
                      <td data-receivable-id="{{ $receivable->id }}">
@if ($session->closed)
                        @if ($receivable->deposit)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($receivable->deposit)
                        <a href="javascript:void(0)" class="btn-del-deposit"><i class="fa fa-toggle-on"></i></a>
@else
                        <a href="javascript:void(0)" class="btn-add-deposit"><i class="fa fa-toggle-off"></i></a>
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
