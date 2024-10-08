                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.auctions') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" id="btn-remitments-back">{{ __('meeting.titles.remitments') }}</button>
                      </div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" id="btn-auctions-refresh"><i class="fa fa-sync"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-auctions-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive" id="meeting-auctions-page">
                  </div> <!-- End table -->
