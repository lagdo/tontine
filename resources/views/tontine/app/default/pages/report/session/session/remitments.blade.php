                  <div class="row mb-2">
                    <div class="col">
                      <div class="section-title mt-0">{{ __('meeting.titles.remitments') }}</div>
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
                          <td class="currency">{{ $pool->paid_count }}@if ($pool->remit_planned && !$pool->remit_auction) / {{ $pool->total_count }}@endif</td>
                          <td class="currency">
                            {{ $locale->formatMoney($pool->paid_count > 0 ? $pool->paid_amount : 0) }}
@isset($auctions[$pool->id])
                            <br/>{{ __('meeting.remitment.labels.auction') }}: {{ $locale->formatMoney($auctions[$pool->id]) }}
@endisset
                          </td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
