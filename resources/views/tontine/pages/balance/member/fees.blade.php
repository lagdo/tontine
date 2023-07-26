                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.fixed') }}</div>
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
@foreach($fees as $fee)
                        <tr>
                          <td>{{ $fee->name }}@isset($fee->session)<br/>{{ $fee->session->title }}@endisset</td>
                          <td>{{ $fee->amount }}</td>
                          <td><i class="fa fa-toggle-{{ $fee->paid ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
