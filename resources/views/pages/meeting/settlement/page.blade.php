                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td data-target-id="{{ $charge->is_fee ? $member->id : $member->bill->id }}">
@if ($member->paid)
                            <a href="javascript:void(0)" class="btn-del-settlement"><i class="fa fa-toggle-on"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-add-settlement"><i class="fa fa-toggle-off"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
