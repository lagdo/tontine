                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td data-member-id="{{ $member->id }}">
@if ($session->closed)
                            @if ($member->fine_bills_count > 0)<i class="fa fa-toggle-on"></i>@else<i class="fa fa-toggle-off">@endif
@elseif ($member->fine_bills_count > 0)
                            <a href="javascript:void(0)" class="btn-del-fine"><i class="fa fa-toggle-on"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-add-fine"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
