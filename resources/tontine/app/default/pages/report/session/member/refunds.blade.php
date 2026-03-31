                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="table-item-menu">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($refunds as $refund)
                        <tr>
                          <td>
                            <div>{{ __('meeting.loan.labels.' . $refund->debt->type_str)
                              }} @if ($refund->is_partial) ({{ __('meeting.refund.labels.partial') }})@endif </div>
                            <div>{{ $refund->debt->session->title }}</div>
                          </td>
                          <td class="currency">{{ $locale->formatMoney($refund->amount) }}</td>
                          <td class="table-item-menu"><i class="fa fa-toggle-{{ true ? 'on' : 'off' }}"></i></td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
