                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.fines') }}</div>
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
@foreach($fines as $fine)
                        <tr>
                          <td>{{ $fine->bill->charge }}<br/>{{ $fine->session->title }}</td>
                          <td>{{ $fine->amount }}</td>
                          <td><i class="fa fa-toggle-{{ $fine->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
