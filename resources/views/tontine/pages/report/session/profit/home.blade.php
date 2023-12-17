@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.profits') }} :: {!! $fund !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! Form::text('profit_amount', $profit, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'profit_amount']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-profits-refresh"><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row" id="profit_distribution_details">
                  </div>
                  <div class="table-responsive mt-2" id="meeting-profits-page">
                  </div> <!-- End table -->
