@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $fundId = pm()->select('report-savings-fund-id')->toInt();
  $rqProfit = rq(Ajax\App\Report\Session\Saving\Profit::class);
@endphp
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group">
                        {!! $html->select('fund_id', $funds, 0)->id('report-savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqProfit->fund($fundId))>{!!
                            __('tontine.report.actions.show') !!}</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="currency">{{ $saving->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->total_amount) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
