                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th>{!! __('common.labels.type') !!}</th>
                      <th>{!! __('common.labels.amount') !!}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($debts as $debt)
                    <tr>
                      <td>{{ $debt->member->name }}</td>
                      <td>{{ __('tontine.loan.labels.' . $debt->type) }}</td>
                      <td>{{ $debt->amount }}</td>
@if ($debt->refund_count)
                      <td data-refund-id="{{ $debt->refund->id }}">
                        <a href="javascript:void(0)" class="btn-del-refund"><i class="fa fa-toggle-on"></i></a>
                      </td>
@else
                      <td data-debt-id="{{ $debt->type }}:{{ $debt->id }}">
                        <a href="javascript:void(0)" class="btn-add-refund"><i class="fa fa-toggle-off"></i></a>
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
