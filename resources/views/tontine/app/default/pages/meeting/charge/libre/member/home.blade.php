@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{{ $charge->name }}</div>
                    </div>
                    <div class="col-auto" id="member-libre-settlements-total">
                    </div>
                    <div class="col-auto">
                      <div class="btn-group float-right ml-2 mb-2" role="group">
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-back"><i class="fa fa-arrow-left"></i></button>
                        <button type="button" class="btn btn-primary" id="btn-fee-libre-filter"><i class="fa fa-filter"></i></button>
                      </div>
                    </div>
                  </div>
                  <div class="row mb-1">
                    <div class="col">
                      <div class="input-group">
                        {!! $htmlBuilder->text('search', '')->id('txt-fee-member-search')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fee-libre-search"><i class="fa fa-search"></i></button>
                        </div>
                      </div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group input-group-sm float-right mb-1 mr-0 pr-0">
                        <div class="input-group-prepend">
                          <div class="input-group-text" style="height:36px;">
                            {!! $htmlBuilder->checkbox('', $paid, '1')->id('check-fee-libre-paid') !!}
                          </div>
                        </div>
                        {!! $htmlBuilder->label(__('common.labels.paid'), '')->class('form-control')
                          ->attribute('style', 'height:36px; padding:5px 15px;') !!}
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive" id="meeting-fee-libre-members">
                  </div> <!-- End table -->
