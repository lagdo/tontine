@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {{ __('meeting.titles.disbursements') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:30%;">{{ __('meeting.labels.category') }}</th>
                          <th style="width:20%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($disbursements as $disbursement)
                        <tr>
                          <td>{{ $disbursement->member ? $disbursement->member->name : '' }}</td>
                          <td>{{ $disbursement->category->name }}</td>
                          <td style="text-align:right;">{{ $locale->formatMoney($disbursement->amount, true) }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $total->total_count }}</td>
                          <td>{{ $locale->formatMoney($total->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
