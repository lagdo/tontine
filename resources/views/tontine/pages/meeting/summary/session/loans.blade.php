                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th>{{ __('tontine.loan.labels.principal') }}</th>
                          <th>{{ __('tontine.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $loan->amount }}</td>
                          <td>{{ $loan->interest }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
