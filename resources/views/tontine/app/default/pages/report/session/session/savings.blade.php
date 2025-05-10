                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{!! __('meeting.titles.savings') !!}</div>
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
                          <td class="currency">{{ $saving->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($saving->total_amount) }}</td>
                        </tr>
@if ($startingFunds->count() > 0)
                        <tr>
                          <td>{{ __('meeting.saving.labels.start_amount') }}</td>
                          <td class="currency">{{ $startingFunds->count() }}</td>
                          <td class="currency">{{ $locale->formatMoney($startingFunds->sum('start_amount')) }}</td>
                        </tr>
@endif
@if ($endingFunds->count() > 0)
                        <tr>
                          <td>{{ __('meeting.saving.labels.end_amount') }}</td>
                          <td class="currency">{{ $endingFunds->count() }}</td>
                          <td class="currency">{{ $locale->formatMoney($endingFunds->sum('end_amount')) }}</td>
                        </tr>
@endif
                      </tbody>
                    </table>
                  </div> <!-- End table -->
