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
                          <th>&nbsp;</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($pools as $pool)
@if($session->disabled($pool))
                        <tr style="background-color:rgba(0, 0, 0, 0.02)">
                          <td>{{ $pool->title }}</td>
                          <td>{{ $tontine->is_libre ? __('tontine.labels.types.libre') : $locale->formatMoney($pool->amount, true) }}</td>
                          <td></td>
                          <td></td>
                        </tr>
@else
                        <tr>
                          <td>{{ $pool->title }}</td>
                          <td>{{ $tontine->is_libre ? __('tontine.labels.types.libre') : $locale->formatMoney($pool->amount, true) }}</td>
                          <td>{{ $pool->paid_count }}/{{ $pool->total_count }}</td>
                          <td class="currency">{{ $locale->formatMoney($pool->paid_amount, true) }}</td>
                        </tr>
@endif
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
