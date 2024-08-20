                    <div class="row">
                      <div class="col-auto">
                        <div class="section-title mt-0">{{ __('meeting.titles.partial-refunds') }}</div>
                      </div>
@if($session->opened)
                      <div class="col">
@if($funds->count() > 1)
                        <div class="input-group mb-2">
                          {!! $htmlBuilder->select('fund_id', $funds, 0)->id('partial-refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-partial-refunds-fund"><i class="fa fa-arrow-right"></i></button>
                            <button type="button" class="btn btn-primary" id="btn-partial-refunds-add"><i class="fa fa-plus"></i></button>
                            <button type="button" class="btn btn-primary" id="btn-partial-refunds-refresh"><i class="fa fa-sync"></i></button>
                          </div>
                        </div>
@else
                        <div class="btn-group float-right ml-2 mb-2" role="group">
                          <button type="button" class="btn btn-primary" id="btn-partial-refunds-add"><i class="fa fa-plus"></i></button>
                          <button type="button" class="btn btn-primary" id="btn-partial-refunds-refresh"><i class="fa fa-sync"></i></button>
                        </div>
@endif
                      </div>
@endif
                    </div>
                    <div class="table-responsive" id="meeting-partial-refunds-page">
                    </div> <!-- End table -->
