                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ $pool->title }} - {{ __('meeting.titles.remitments') }}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-remitments-back"><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($payables as $payable)
                        <tr>
                          <td>{{ $payable->title === '__' ? __('tontine.loan.labels.amount_to_bid') : $payable->title }}</td>
                          <td>{{ $payable->amount }}@if ($payable->paid > 0)<br/>{{ $payable->paid }}@endif</td>
                          <td class="table-item-menu" data-subscription-id="{{ $payable->id }}">
@if ($payable->available)
                            <a href="javascript:void(0)" class="btn-add-remitment"><i class="fa fa-toggle-off"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-del-remitment"><i class="fa fa-toggle-on"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
