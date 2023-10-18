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
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('meeting.loan.labels.principal') }}</th>
                          <th class="currency">{{ __('meeting.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($loans as $loan)
                        <tr>
                          <td>{{ $loan->session->title }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->principal, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($loan->interest, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
