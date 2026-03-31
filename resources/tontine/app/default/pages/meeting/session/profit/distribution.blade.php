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
  $memberPercent = $profitSum === 0 ? 0 : $memberProfit / $profitSum;
  $rowSpan = $transfers->count() + 1;
@endphp
                        <tr>
                          <td class="align-top" rowspan="{{ $rowSpan }}">
                            <b>{{ $transfers[0]->member->name }}</b>
                          </td>
                          <td data-label="&nbsp;">&nbsp;</td>
                          <td class="currency" data-label="{!! __('common.labels.amount') !!}">
                            <b>{{ $locale->formatMoney($transfers->sum('amount')) }}</b>
                          </td>
                          <td class="currency">
                            <b>{{ $transfers->sum('parts') }} ({{ sprintf('%.2f', $memberPercent * 100) }}%)</b>
                          </td>
                          <td class="currency align-top" rowspan="{{ $rowSpan }}">
                            <b>{{ $locale->formatMoney((int)($profitAmount * $memberPercent)) }}</b>
                          </td>
                        </tr>
@foreach ($transfers as $transfer)
@php
  $transferParts = $distribution->partAmount === 0 ? 0 : $transfer->amount / $distribution->partAmount;
@endphp
                        <tr>
                          <td>
                            <div>{!! $transfer->type !!}</div>
                            <div>{{ $transfer->session->title }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $locale->formatMoney($transfer->amount * $transfer->coef) }}</div>
                            <div>{{ $transfer->duration }}</div>
                          </td>
                          <td class="currency">
                            <div>{{ $transferParts }}*{{ $transfer->duration }}</div>
                            <div>={{ $transfer->parts }}</div>
                          </td>
                        </tr>
@endforeach
@endforeach
                      </tbody>
                    </table>
