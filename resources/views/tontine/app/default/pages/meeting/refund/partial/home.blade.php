                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ __('meeting.titles.partial-refunds') }}</div>
                    </div>
@if($session->opened)
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" id="btn-partial-refunds-add"><i class="fa fa-plus"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-partial-refunds-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive" id="meeting-partial-refunds-page">
                  </div> <!-- End table -->
