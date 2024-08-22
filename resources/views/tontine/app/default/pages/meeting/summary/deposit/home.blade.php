@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="row">
                    <div class="col-auto">
                      <div class="section-title mt-0">{!! __('meeting.titles.deposits') !!}</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('common.labels.title') !!}</th>
                          <th>&nbsp;</th>
                          <th>&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($pools as $pool)
                        <tr>
                          <td>{{ $pool->title }}<br/>{{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount, true) : __('tontine.labels.types.libre') }}</td>
                          <td class="currency">{{ $pool->recv_paid }}/{{ $pool->recv_count }}@if ($pool->amount_recv > 0)<br/>{{
                            $locale->formatMoney($pool->amount_recv, true) }}@endif</td>
                          <td class="table-item-menu">&nbsp;</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->