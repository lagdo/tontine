                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group input-group-sm float-right mb-1 mr-0 pr-0">
                        <div class="input-group-prepend">
                          <div class="input-group-text">
                            {!! Form::checkbox('', '1', $paid, ['id' => 'check-fee-libre-paid']) !!}
                          </div>
                        </div>
                        {!! Form::label('', __('common.labels.paid'), ['class' => 'form-control']) !!}
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-fee-libre-members">
                  </div> <!-- End table -->
