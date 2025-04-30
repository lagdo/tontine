                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('tontine.report.titles.bills.total') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('tontine.report.titles.amounts.cashed') }}</th>
                          <th class="currency">{{ __('tontine.report.titles.amounts.disbursed') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($charges->filter(fn($charge) => $charge->total_count > 0) as $charge)
                        <tr>
                          <td>{{ $charge->name }}<br/>{{ $charge->has_amount ?
                            $locale->formatMoney($charge->amount) : __('tontine.labels.fees.variable') }}</td>
                          <td class="currency">@if ($charge->total_count > 0){{
                            $locale->formatMoney($charge->total_amount) }}<br/>{{
                            $charge->total_count }}@else &nbsp; @endif</td>
                          <td class="currency">@if ($charge->outflow !== null){{
                            $locale->formatMoney($charge->outflow->total_amount) }}<br/>{{
                            $charge->outflow->total_count }}@else &nbsp; @endif</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
