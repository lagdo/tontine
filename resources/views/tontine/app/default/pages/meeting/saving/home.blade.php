                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-savings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="row">
                    <div class="col" id="meeting-savings-total">
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! Form::select('fund', $funds, $fundId, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'savings-fund-id']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-savings-fund"><i class="fa fa-filter"></i></button>
@if($session->opened)
                          <button type="button" class="btn btn-primary" id="btn-savings-edit"><i class="fa fa-edit"></i></button>
@endif
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-savings-page">
                  </div> <!-- End table -->
