                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.deposits') }}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th class="currency">&nbsp;</th>
                          <th class="currency">{{ __('common.labels.total') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($pools as $pool)
                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount) : __('tontine.labels.types.libre') }}</td>
                          <td class="currency">
                            @include('tontine::pages.report.session.session.deposit', [
                              'pool' => $pool,
                            ])
                          </td>
                          <td class="currency">
                            {{ $locale->formatMoney($pool->recv_amount) }}
                            @if ($pool->prev_late > 0 || $pool->next_early > 0)<br/>&nbsp;@endif
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
