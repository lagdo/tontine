                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th>{!! __('common.labels.name') !!}</th>
                      <th>{!! __('common.labels.paid') !!}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($payables as $payable)
                    <tr>
                      <td>{{ $payable->subscription->member->name }}</td>
                      <td data-payable-id="{{ $payable->id }}">
@if ($payable->remittance)
                          <a href="javascript:void(0)" class="btn-del-remittance"><i class="fa fa-toggle-on"></i></a>
@else
                          <a href="javascript:void(0)" class="btn-add-remittance"><i class="fa fa-toggle-off"></i></a>
@endif
                      </td>
                    </tr>
@endforeach
                  </tbody>
                </table>
                <nav>{!! $pagination !!}</nav>
