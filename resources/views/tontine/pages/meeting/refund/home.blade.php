                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-refunds-refresh"><i class="fa fa-sync"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-refunds-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive" id="meeting-debts-page">
                  </div> <!-- End table -->
