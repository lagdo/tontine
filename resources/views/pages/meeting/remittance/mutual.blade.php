                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ $fund->title }} - {{ __('meeting.titles.remittances') }}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remittances-back"><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-fund-remittances">
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
                  </div> <!-- End table -->
