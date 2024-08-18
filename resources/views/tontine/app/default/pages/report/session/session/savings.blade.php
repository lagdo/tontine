@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund_id', $funds, 0)->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;')->id('report-savings-fund-id') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" id="btn-report-fund-savings">{!!
                            __('tontine.report.actions.show') !!}</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ $saving->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
