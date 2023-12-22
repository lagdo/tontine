@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {{ __('meeting.titles.refunds') }}
                  </div>
                  <div class="table">
                    <table>
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
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $locale->formatMoney($total->principal, true) }} + {{
                            $locale->formatMoney($total->interest, true) }}</td>
                          <td>{{ $locale->formatMoney($total->principal + $total->interest, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
