                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
@if($session->opened)
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! Form::select('fund', $funds, $fundId, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'savings-fund-id']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-savings-fund"><i class="fa fa-arrow-down"></i></button>
                          <button type="button" class="btn btn-primary" id="btn-savings-edit"><i class="fa fa-arrow-right"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-savings-refresh"><i class="fa fa-sync"></i></button>
                      </div>
                    </div>
@endif
                  </div>
                  <div class="table-responsive" id="meeting-savings-page">
                  </div> <!-- End table -->
