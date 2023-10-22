                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }} - {{ __('common.actions.add') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group" aria-label="">
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row align-items-center">
                    <div class="col-auto">
                      <div class="input-group ml-2 mb-2">
                        {!! Form::text('search', '', ['class' => 'form-control', 'id' => 'txt-fee-member-search']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fee-libre-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col">&nbsp;</div>
                    <div class="col-auto" style="margin-right: 30px">
                      {!! Form::checkbox('', '1', false, ['id' => 'check-fee-libre-paid']) !!}
                      {!! Form::label('', __('common.labels.paid'), ['class' => 'form-check-label']) !!}
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-fee-libre-members">
                  </div> <!-- End table -->
