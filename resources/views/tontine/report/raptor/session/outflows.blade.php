                  <div class="section-title">
                    {{ __('meeting.titles.outflows') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;">{{ __('meeting.labels.category') }}</th>
                          <th style="width:20%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($outflows as $outflow)
                        <tr>
                          <td>{{ $outflow->member ? $outflow->member->name : '' }}</td>
                          <td>{{ $outflow->category->name }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($outflow->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $total->total_count }}</td>
                          <td>{{ $locale->formatMoney($total->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
