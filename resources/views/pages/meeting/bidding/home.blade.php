                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">@isset($fund){{ $fund->title }} - {{ __('meeting.titles.remittances') }}@else{{ __('meeting.titles.biddings') }}@endisset</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-biddings-back"><i class="fa fa-arrow-left"></i></button>
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
@foreach ($biddings as $bidding)
                        <tr>
                          <td>{{ $bidding->title === '__' ? __('tontine.bidding.labels.amount_to_bid') : $bidding->title }}</td>
                          <td>{{ $bidding->amount }}@if ($bidding->paid > 0)<br/>{{ $bidding->paid }}@endif</td>
                          <td class="table-item-menu" data-subscription-id="{{ $bidding->id }}">
@if ($bidding->available)
                            <a href="javascript:void(0)" class="btn-bidding-add"><i class="fa fa-toggle-off"></i></a>
@else
                            <a href="javascript:void(0)" class="btn-bidding-delete"><i class="fa fa-toggle-on"></i></a>
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
