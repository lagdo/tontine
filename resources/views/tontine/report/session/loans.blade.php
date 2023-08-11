@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="pagebreak"></div>

                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.loans') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th class="currency">{{ __('meeting.loan.labels.principal') }}</th>
                          <th class="currency">{{ __('meeting.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($loans as $loan)
                        <tr>
                          <td>{{ $loan->member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->principal, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->interest, true) }}</td>
                        </tr>
@endforeach
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td class="currency">{{ $locale->formatMoney($total->principal, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($total->interest, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
