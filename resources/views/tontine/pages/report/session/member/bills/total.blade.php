@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('tontine.report.titles.bills.total') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($charges as $charge)
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->has_amount ?
                            $locale->formatMoney($charge->amount, true) : __('tontine.labels.fees.variable') }}</td>
                          <td>{{ $charge->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($charge->total_amount, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
