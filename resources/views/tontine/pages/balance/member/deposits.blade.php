                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th>{{ __('common.labels.amount') }}</th>
                          <th>{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($receivables as $receivable)
                        <tr>
                          <td>{{ $receivable->subscription->pool->title }}</td>
                          <td>{{ $receivable->amount }}</td>
                          <td><i class="fa fa-toggle-{{ $receivable->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
