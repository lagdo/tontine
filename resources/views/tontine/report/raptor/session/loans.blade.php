@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {{ __('meeting.titles.loans') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;text-align:right;">{{ __('meeting.loan.labels.interest') }}</th>
                          <th style="width:20%;text-align:right;">{{ __('meeting.loan.labels.principal') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($loans as $loan)
                        <tr>
                          <td>{{ $loan->member->name }}</td>
                          <td style="text-align:right;">{{ __('meeting.loan.interest.' . $loan->interest_type) }}: {{ $loan->fixed_interest ?
                            $locale->formatMoney($loan->interest, true) : ($loan->interest_rate / 100) . '%' }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($loan->principal, true) }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>&nbsp;</td>
                          <td style="width:20%;">{{ $locale->formatMoney($total->principal, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
