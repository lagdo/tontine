                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="section-title mt-0">{{ $charge->name }} - {{ __('meeting.titles.settlements') }}</div>
                    </div>
                    <div class="col">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-settlements-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-settlements-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-charge-bills">
                  </div> <!-- End table -->
