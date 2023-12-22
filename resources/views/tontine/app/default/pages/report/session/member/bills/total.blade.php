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
                          <th class="currency">{{ __('tontine.report.titles.amounts.cashed') }}</th>
                          <th class="currency">{{ __('tontine.report.titles.amounts.disbursed') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($charges->filter(function($charge) { return $charge->total_count > 0; }) as $charge)
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->has_amount ?
                            $locale->formatMoney($charge->amount, true) : __('tontine.labels.fees.variable') }}</td>
                          <td class="currency">@if ($charge->total_count > 0){{
                            $locale->formatMoney($charge->total_amount, true) }}<br/>{{
                            $charge->total_count }}@else &nbsp; @endif</td>
                          <td class="currency">@if ($charge->disbursement !== null){{
                            $locale->formatMoney($charge->disbursement->total_amount, true) }}<br/>{{
                            $charge->disbursement->total_count }}@else &nbsp; @endif</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
