                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! !$fund ? __('tontine.fund.labels.default') : $fund->title !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-saving-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-saving-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-saving-members">
                  </div> <!-- End table -->
