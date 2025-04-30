                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.outflows') }}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
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
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th style="text-align:right;">{{ $total->total_count }}</th>
                          <th style="text-align:right;">{{ $locale->formatMoney($total->total_amount, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
