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
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($transfers as $transfer)
                        <tr>
                          <td>
                            <div>{!! $transfer->type !!}</div>
                            <div>{{ $transfer->session->title }}</div>
                          </td>
                          <td class="currency">{{ $locale->formatMoney($transfer->amount) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
