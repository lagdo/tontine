                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">@isset($fund){{ $fund->title }} - {{ __('meeting.titles.remittances') }}@else{{ __('meeting.titles.biddings') }}@endisset</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-biddings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>{!! __('common.labels.amount') !!}</th>
                          <th>{!! __('common.labels.price') !!}</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('tontine.bidding.labels.amount_to_bid') }}</td>
                          <td>{{ $amountAvailable }}</td>
                          <td>&nbsp;</td>
@if($session->closed)
                          <td>&nbsp;</td>
@else
                          <td class="table-item-menu">
                            <a href="javascript:void(0)" class="btn-bidding-add"><i class="fa fa-toggle-off"></i></a>
                          </td>
@endif
                        </tr>
@foreach ($biddings as $bidding)
                        <tr>
                          <td>{{ $bidding->title }}</td>
                          <td>{{ $bidding->amount }}</td>
                          <td>{{ $bidding->paid }}</td>
@if($session->closed)
                          <td>&nbsp;</td>
@else
                          <td class="table-item-menu" data-subscription-id="{{ $bidding->id }}">
                            <a href="javascript:void(0)" class="btn-bidding-delete"><i class="fa fa-toggle-on"></i></a>
                          </td>
@endif
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
