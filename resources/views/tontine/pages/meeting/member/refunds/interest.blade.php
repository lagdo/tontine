                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }} - {{ __('tontine.loan.labels.interest') }}</div>
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
@foreach($loans as $loan)
                        <tr>
                          <td>{{ $loan->session->title }}@if(($loan->refund))<br/>{{ $loan->refund->session->title }}@endif</td>
                          <td>{{ $loan->interest }}</td>
                          <td><i class="fa fa-toggle-{{ $loan->refunded ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
