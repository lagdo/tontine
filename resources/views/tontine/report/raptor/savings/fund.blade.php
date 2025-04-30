      <div class="section section-title">
        {!! $fund->title !!} :: {{ $locale->formatMoney($distribution->profitAmount, true)
          }}, {{ __('meeting.profit.distribution.parts', [
            'parts' => $distribution->savings->sum('parts'),
          ]) }}
      </div>
@if ($distribution->rewarded->count() > 1)
      <div class="section section-subtitle">
        {!! __('meeting.profit.distribution.basis', [
          'unit' => $locale->formatMoney($distribution->partAmount, true),
        ]) !!}
      </div>
@endif
      <div class="table">
        <table>
          <thead>
            <tr>
              <th>{!! __('meeting.labels.member') !!}</th>
              <th>{!! __('meeting.labels.saving') !!}</th>
              <th style="text-align:right;">{!! __('meeting.labels.duration') !!}</th>
              <th style="text-align:right;">{!! __('meeting.labels.saving') !!}</th>
              <th style="text-align:right;">{!! __('meeting.labels.distribution') !!}</th>
              <th style="text-align:right;">{!! __('meeting.labels.profit') !!}</th>
            </tr>
          </thead>
          <tbody>
@foreach ($distribution->savings->groupBy('member_id') as $savings)
            <tr>
              <td rowspan="{{ $savings->count() + 1 }}">{{ $savings[0]->member->name }}</td>
              <td class="report-savings-session">&nbsp;</td>
              <td class="report-savings-count">&nbsp;</td>
              <td class="report-savings-amount">
                <b>{{ $locale->formatMoney($savings->sum('amount'), true) }}</b>
              </td>
              <td class="report-savings-amount">
                <b>{{ $savings->sum('parts') }} ({{ sprintf('%.2f', $savings->sum('percent')) }}%)</b>
              </td>
              <td class="report-savings-amount">
                <b>{{ $locale->formatMoney($savings->sum('profit'), true) }}</b>
              </td>
            </tr>
@foreach ($savings as $saving)
            <tr>
              <td class="report-savings-session">{{ $saving->session->title }}</td>
              <td class="report-savings-count">{{ $saving->duration }}</td>
              <td class="report-savings-amount">
                {{ $locale->formatMoney($saving->amount, true) }}
              </td>
              <td class="report-savings-amount">
                {{ $saving->parts }} ({{ sprintf('%.2f', $saving->percent) }}%)
              </td>
              <td class="report-savings-amount">
                {{ $locale->formatMoney($saving->profit, true) }}
              </td>
            </tr>
@endforeach
@endforeach
          </tbody>
        </table>
      </div> <!-- End table -->
