@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.fundings') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $funding->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($funding->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
