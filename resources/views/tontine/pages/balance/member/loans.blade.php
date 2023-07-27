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
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="currency">{{ __('tontine.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($loans as $loan)
                        <tr>
                          <td class="currency">{{ $locale->formatMoney($loan->amount, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->interest, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
