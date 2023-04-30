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
                      <td>{{ $debt->loan->member->name }}<br/>{{ $debt->loan->session->title }}</td>
                      <td>{{ $debt->amount }}</td>
@if ($session->closed)
                      <td>
                        @if ($debt->refund) <i class="fa fa-toggle-on"></i> @else <i class="fa fa-toggle-off"> @endif
                      </td>
@elseif ($debt->refund)
                      <td data-refund-id="{{ $debt->refund->id }}">
                        <a href="javascript:void(0)" class="btn-del-{{ $type }}-refund"><i class="fa fa-toggle-on"></i></a>
                      </td>
@else
                      <td data-debt-id="{{ $debt->id }}">
                        <a href="javascript:void(0)" class="btn-add-{{ $type }}-refund"><i class="fa fa-toggle-off"></i></a>
                      </td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
