@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row align-items-center">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
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
@foreach($pools as $pool)
                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount, true) : __('tontine.labels.types.libre') }}</td>
                          <td>{{ $pool->paid_count }}/{{ $pool->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($pool->paid_amount, true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
