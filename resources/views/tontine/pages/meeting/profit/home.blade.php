@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.profits') }}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! Form::select('fund_id', $funds, 0, ['class' => 'form-control', 'id' => 'profit_fund_id']) !!}
                        {!! Form::text('profit_amount', $profit, ['class' => 'form-control', 'id' => 'profit_amount_edit']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-profits-refresh"><i class="fa fa-sync"></i></button>
@if($session->opened)
                          <button type="button" class="btn btn-primary" id="btn-profits-save"><i class="fa fa-save"></i></button>
@endif
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row" id="profit_distribution_details">
                  </div>
                  <div class="table-responsive mt-2" id="meeting-profits-page">
                  </div> <!-- End table -->
