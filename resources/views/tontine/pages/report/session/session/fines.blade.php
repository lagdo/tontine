@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.charge.titles.variable') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($fines as $fine)
                        <tr>
                          <td>{{ $fine->name }}<br/>{{ $fine->has_amount ?
                            $locale->formatMoney($fine->amount, true) : __('tontine.labels.fees.variable') }}</td>
                          <td>{{ $fine->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($fine->total_amount, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
