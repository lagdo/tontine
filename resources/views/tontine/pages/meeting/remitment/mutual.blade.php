                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.name') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.paid') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
                        <tr>
                          <td>{{ $payable->subscription->member->name ?? __('tontine.remitment.labels.not-assigned') }}</td>
                          <td>{{ $payable->amount }}</td>
                          <td data-payable-id="{{ $payable->id }}">
@if ($payable->remitment)
                            <a href="javascript:void(0)" class="btn-del-remitment"><i class="fa fa-toggle-on"></i></a>
@elseif ($payable->id > 0)
                            <a href="javascript:void(0)" class="btn-add-remitment"><i class="fa fa-toggle-off"></i></a>
@else
                            <i class="fa fa-toggle-off"></i>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
