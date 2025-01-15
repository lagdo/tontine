@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $fundId = pm()->select('report-savings-fund-id')->toInt();
  $rqFund = rq(Ajax\App\Report\Session\Saving\Fund::class);
@endphp
                  <div class="row">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
                    </div>
                    <div class="col-auto">
                      <div class="input-group mb-2">
                        {!! $htmlBuilder->select('fund_id', $funds, 0)->id('report-savings-fund-id')
                          ->class('form-control')->attribute('style', 'height:36px; padding:5px 15px;') !!}
                        <div class="input-group-append">
                          <button type="button" class="btn btn-primary" @jxnClick($rqFund->fund($fundId, 'report'))>{!!
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
