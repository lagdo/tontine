@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.refunds') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('meeting.loan.labels.principal') }}</th>
                          <th class="currency">{{ __('meeting.loan.labels.interest') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td class="currency">{{ $locale->formatMoney($refund->principal) }}</td>
                          <td class="currency">{{ $locale->formatMoney($refund->interest) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
