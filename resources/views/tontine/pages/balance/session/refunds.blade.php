                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th>{{ __('tontine.loan.labels.principal') }}</th>
                          <th>{{ __('tontine.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $refund->amount }}</td>
                          <td>{{ $refund->interest }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
