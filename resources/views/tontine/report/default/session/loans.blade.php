@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{{ __('meeting.titles.loans') }}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
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
                        <tr>
                          <th>{{ __('common.labels.total') }}</th>
                          <th>&nbsp;</th>
                          <th style="width:20%;text-align:right;">{{ $locale->formatMoney($total->principal, true) }}</th>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
