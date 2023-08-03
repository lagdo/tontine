@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th class="currency">{{ __('meeting.loan.labels.principal') }}</th>
                          <th class="currency">{{ __('meeting.loan.labels.interest') }}</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td class="currency">{{ $locale->formatMoney($loan->principal, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->interest, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->principal + $loan->interest, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
