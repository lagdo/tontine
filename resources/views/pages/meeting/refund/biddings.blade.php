                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th>{!! __('common.labels.amount') !!}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($biddings as $bidding)
                    <tr>
                      <td>{{ $bidding->member->name }}</td>
                      <td>{{ $bidding->amount }}</td>
@if ($bidding->refund)
                      <td data-refund-id="{{ $bidding->refund->id }}">
                        <a href="javascript:void(0)" class="btn-del-refund"><i class="fa fa-toggle-on"></i></a>
                      </td>
@else
                      <td data-bidding-id="{{ $bidding->id }}">
                        <a href="javascript:void(0)" class="btn-add-refund"><i class="fa fa-toggle-off"></i></a>
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
