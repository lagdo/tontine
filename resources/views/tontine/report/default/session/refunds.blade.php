@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.refunds') }}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;">{{ __('meeting.labels.session') }}</th>
                          <th style="width:20%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($refunds as $refund)
                        <tr>
                          <td>{{ $refund->member->name }}</td>
                          <td>{{ __('meeting.loan.labels.' . $refund->debt->type_str) }}@if ($refund->is_partial) ({{
                            __('meeting.refund.labels.partial') }})@endif<br/>{{ $refund->debt->session->title }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($refund->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <td style="text-align:right;">{{ $locale->formatMoney($total->principal, true)
                            }} + {{ $locale->formatMoney($total->interest, true) }}</td>
                          <th style="text-align:right;">{{ $locale->formatMoney($total->principal + $total->interest, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
