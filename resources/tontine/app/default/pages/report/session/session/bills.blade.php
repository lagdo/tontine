                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ $title }}</div>
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
@foreach($charges as $charge)
                        <tr>
                          <td>
                            <div>{{ $charge->name }}</div>
                            <div>{{ $charge->has_amount ?
                              $locale->formatMoney($charge->amount) : __('tontine.labels.fees.variable') }}</div>
                          </td>
                          <td class="currency">
@if ($charge->total_count > 0)
                            <div>{{ $locale->formatMoney($charge->total_amount) }}</div>
                            <div>{{ $charge->total_count }}</div>
@else
                            &nbsp;
@endif
                          </td>
                          <td class="currency">
@if ($charge->outflow !== null)
                            <div>{{ $locale->formatMoney($charge->outflow->total_amount) }}</div>
                            <div>{{ $charge->outflow->total_count }}</div>
@else
                            &nbsp;
@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
