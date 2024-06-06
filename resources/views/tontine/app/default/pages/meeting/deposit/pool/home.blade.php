                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">
                        {{ $pool->title }}<br/> {{ __('meeting.titles.deposits')
                          }} (<span id="meeting-deposits-total"></span>) <span id="meeting-deposits-action"></span>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-deposits-back"><i class="fa fa-arrow-left"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-pool-deposits">
                  </div> <!-- End table -->
