                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">@isset($pool){{ $pool->title }} - {{ __('meeting.titles.remittances') }}@else{{ __('meeting.titles.loans') }}@endisset</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-loans-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.interest') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('tontine.loan.labels.amount_to_lend') }}</td>
                          <td>{{ $amountAvailable }}</td>
                          <td>&nbsp;</td>
@if(!$session->opened)
                          <td>&nbsp;</td>
@else
                          <td class="table-item-menu">
                            <a href="javascript:void(0)" class="btn-loan-add"><i class="fa fa-toggle-off"></i></a>
                          </td>
@endif
                        </tr>
@foreach ($loans as $loan)
                        <tr>
                          <td>{{ $loan->title }}</td>
                          <td>{{ $loan->amount }}</td>
                          <td>{{ $loan->paid }}</td>
@if(!$session->opened)
                          <td>&nbsp;</td>
@else
                          <td class="table-item-menu" data-subscription-id="{{ $loan->id }}">
                            <a href="javascript:void(0)" class="btn-loan-delete"><i class="fa fa-toggle-on"></i></a>
                          </td>
@endif
                        </tr>
@endforeach
@if($session->closed)
                        <tr>
                          <td>{!! __('common.labels.total') !!}</td>
                          <td>{{ $sum['loan'] }}</td>
                          <td>{{ $sum['paid'] }}</td>
                          <td>&nbsp;</td>
                        </tr>
@endif
                      </tbody>
                    </table>
                  </div> <!-- End table -->
