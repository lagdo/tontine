                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('meeting.labels.member') !!}</th>
                      <th>{!! __('common.labels.amount') !!}</th>
                      <th>&nbsp;</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach($debts as $debt)
                    <tr>
                      <td>{{ $debt->member->name }}<br/>{{ $debt->session->title }}</td>
                      <td>{{ $debt->amount }}<br/>{{ __('tontine.loan.labels.' . $debt->type) }}</td>
@if ($session->closed)
                      <td>
                        @if ($debt->refund_id) <i class="fa fa-toggle-on"></i> @else <i class="fa fa-toggle-off"> @endif
                      </td>
@elseif ($debt->refund_id)
                      <td data-refund-id="{{ $debt->refund_id }}">
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
