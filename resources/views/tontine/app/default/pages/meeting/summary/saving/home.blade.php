                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col" id="meeting-savings-total">
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund', $funds, $fundId)->id('savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-savings-fund"><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-savings-page">
                  </div> <!-- End table -->
