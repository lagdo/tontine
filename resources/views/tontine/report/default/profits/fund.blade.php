@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $distributionSum = $savings->sum('distribution');
  $savings = $savings->groupBy('member_id');
@endphp
                  <div class="row mt-0">
                    <div class="col d-flex justify-content-center">
                      <h5>{!! $fund !!} :: {{ $locale->formatMoney($profitAmount, true) }}, {{
                        __('meeting.profit.distribution.parts', ['parts' => $distributionSum]) }}</h5>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.session') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.duration') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.saving') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.distribution') !!}</th>
                          <th style="text-align:right;">{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $memberSavings)
@php
  $memberDistribution = $memberSavings->sum('distribution');
@endphp
                        <tr>
                          <td rowspan="{{ $memberSavings->count() + 1 }}">{{ $memberSavings[0]->member->name }}</td>
                          <td class="report-profits-session">&nbsp;</td>
                          <td class="report-profits-count">&nbsp;</td>
                          <td class="report-profits-amount"><b>{{ $locale->formatMoney($memberSavings->sum('amount'), true) }}</b></td>
                          <td class="report-profits-amount"><b>{{ $memberDistribution }} ({{ sprintf('%.2f', $distributionSum === 0 ?
                            0 : 100 * $memberDistribution / $distributionSum) }}%)</b></td>
                          <td class="report-profits-amount"><b>{{ $locale->formatMoney($memberSavings->sum('profit'), true) }}</b></td>
                        </tr>
@foreach ($memberSavings as $saving)
                        <tr>
                          <td class="report-profits-session">{{ $saving->session->title }}</td>
                          <td class="report-profits-count">{{ $saving->duration }}</td>
                          <td class="report-profits-amount">{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td class="report-profits-amount">{{ $saving->distribution }} ({{ sprintf('%.2f', $distributionSum === 0 ?
                            0 : 100 * $saving->distribution / $distributionSum) }}%)</td>
                          <td class="report-profits-amount">{{ $locale->formatMoney($saving->profit, true) }}</td>
                        </tr>
@endforeach
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
