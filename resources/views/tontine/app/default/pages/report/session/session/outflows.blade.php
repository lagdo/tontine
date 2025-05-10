                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.outflows') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.count') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td class="currency">{{ $outflow->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($outflow->total_amount) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
