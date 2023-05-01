                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
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
@foreach($debts as $debt)
                        <tr>
                          <td>
                            {{ $debt->loan->session->title }} - {{ __('tontine.loan.labels.' . $debt->type_str) }}
@if(($debt->refund))
                            <br/>{{ $debt->refund->session->title }}
@endif
                          </td>
                          <td>{{ $debt->amount }}</td>
                          <td><i class="fa fa-toggle-{{ $debt->refund ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
