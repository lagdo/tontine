                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }} - {{ __('meeting.titles.fine') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group ml-2 mb-2">
                        {!! Form::text('search', '', ['class' => 'form-control', 'id' => 'txt-fine-search']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fine-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-fine-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fine-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-charge-members">
                  </div> <!-- End table -->
