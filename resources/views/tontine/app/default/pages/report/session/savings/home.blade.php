@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{!! $fund !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! Form::text('fund-profit-amount', $profit, ['class' => 'form-control',
                          'style' => 'height:36px; padding:5px 15px;', 'id' => 'fund-profit-amount']) !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-fund-savings-refresh"><i class="fa fa-sync"></i></button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row" id="report-fund-profits-distribution">
                  </div>
                  <div class="table-responsive mt-2" id="report-fund-savings-page">
                  </div>
