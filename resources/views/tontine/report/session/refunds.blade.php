@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th>{{ __('meeting.labels.session') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($refunds as $refund)
                        <tr>
                          <td>{{ $refund->member->name }}</td>
                          <td>{{ __('meeting.loan.labels.' . $refund->debt->type_str) }}@if ($refund->is_partial) ({{
                            __('meeting.refund.labels.partial') }})@endif<br/>{{ $refund->debt->session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($refund->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <td class="currency">{{ $locale->formatMoney($total->principal, true)
                            }} + {{ $locale->formatMoney($total->interest, true) }}</td>
                          <th class="currency">{{ $locale->formatMoney($total->principal + $total->interest, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
