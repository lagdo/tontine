                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.variable') }}</div>
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
@foreach($bills as $bill)
                        <tr>
                          <td>{{ $bill->charge }}<br/>{{ $bill->session->title }}</td>
                          <td>{{ $bill->amount }}</td>
                          <td><i class="fa fa-toggle-{{ $bill->settlement ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
