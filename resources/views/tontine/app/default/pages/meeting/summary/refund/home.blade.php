                    <div class="row">
                      <div class="col">
                        <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                      </div>
@if($session->opened)
                      <div class="col-auto">
@if($funds->count() > 1)
                        <div class="input-group mb-2">
                          {!! $htmlBuilder->select('fund_id', $funds, 0)->id('refunds-fund-id')
                            ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                          <div class="input-group-append">
                            <button type="button" class="btn btn-primary" id="btn-refunds-fund"><i class="fa fa-arrow-right"></i></button>
                            <button type="button" class="btn btn-primary" id="btn-refunds-filter"><i class="fa fa-filter"></i></button>
                          </div>
                        </div>
@else
                        <div class="btn-group float-right ml-2 mb-2" role="group">
                          <button type="button" class="btn btn-primary" id="btn-refunds-filter"><i class="fa fa-filter"></i></button>
                        </div>
@endif
                      </div>
@endif
                    </div>
                    <div class="table-responsive" id="meeting-debts-page">
                    </div> <!-- End table -->
