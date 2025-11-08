                    <table class="table table-bordered responsive">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>
                            <div>{!! __('meeting.labels.operation') !!}</div>
                            <div>{!! __('meeting.labels.session') !!}</div>
                          </th>
                          <th class="currency">
                            <div>{!! __('common.labels.amount') !!}</div>
                            <div>{!! __('meeting.labels.duration') !!}</div>
                          </th>
                          <th class="currency">{!! __('meeting.labels.distribution') !!}</th>
                          <th class="currency">{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@php
  $profitSum = $distribution->transfers->sum('profit');
  $profitAmount = $distribution->profitAmount;
@endphp
@foreach ($distribution->transfers->groupBy('member_id') as $transfers)
@php
  $memberProfit = $transfers->sum('profit');
  $memberPercent = $memberProfit / $profitSum;
@endphp
                        <tr>
                          <td rowspan="{{ $transfers->count() + 1 }}" style="vertical-align:top;padding-top:20px;">
                            <b>{{ $transfers[0]->member->name }}</b>
                          </td>
                          <td>&nbsp;</td>
                          <td class="currency">
                            <b>{{ $locale->formatMoney($transfers->sum('amount')) }}</b>
                          </td>
                          <td class="currency">
                            <b>{{ $transfers->sum('parts') }} ({{ sprintf('%.2f', $memberPercent * 100) }}%)</b>
                          </td>
                          <td class="currency">
                            <b>{{ $locale->formatMoney((int)($profitAmount * $memberPercent)) }}</b>
                          </td>
                        </tr>
@foreach ($transfers as $transfer)
                        <tr>
                          <td>
                            <div>{{ __($transfer->coef > 0 ? 'meeting.labels.saving' : 'meeting.labels.settlement') }}</div>
                            <div>{{ $transfer->session->title }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($transfer->amount * $transfer->coef) }}</div>
                            <div>{{ $transfer->duration }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $transfer->amount / $distribution->partAmount }}*{{ $transfer->duration }}</div>
                            <div>={{ $transfer->parts }}</div>
                          </td>
                          <td>&nbsp;</td>
                        </tr>
@endforeach
@endforeach
                      </tbody>
                    </table>
