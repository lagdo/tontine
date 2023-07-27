@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.fixed') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">{{ __('common.labels.amount') }}</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($fees as $fee)
                        <tr>
                          <td>{{ $fee->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($fee->amount, true) }}</td>
                          <td class="currency">{{ $locale->formatMoney($fee->total, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
