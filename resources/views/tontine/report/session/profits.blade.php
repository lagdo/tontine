@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $distributionSum = $savings->sum('distribution');
  $savings = $savings->groupBy('member_id');
@endphp
                  <div class="row">
                    <div class="col d-flex justify-content-center flex-nowrap">
                      <div class="section-title mt-0">{{ __('meeting.titles.profits') }} ({{
                        $locale->formatMoney($profitAmount, true) }}, {{
                        __('meeting.profit.distribution.parts', ['parts' => $distributionSum]) }})</div>
                    </div>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.saving') !!}</th>
                          <th>{!! __('meeting.labels.session') !!}</th>
                          <th>{!! __('meeting.labels.duration') !!}</th>
                          <th>{!! __('meeting.labels.distribution') !!}</th>
                          <th>{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($savings as $memberSavings)
@php
  $memberDistribution = $memberSavings->sum('distribution');
@endphp
                        <tr>
                          <td rowspan="{{ $memberSavings->count() + 1 }}">{{ $memberSavings[0]->member->name }}</td>
                          <td><b>{{ $locale->formatMoney($memberSavings->sum('amount'), true) }}</b></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><b>{{ $memberDistribution }} ({{ sprintf('%.2f', $distributionSum === 0 ?
                            0 : 100 * $memberDistribution / $distributionSum) }}%)</b></td>
                          <td><b>{{ $locale->formatMoney($memberSavings->sum('profit'), true) }}</b></td>
                        </tr>
@foreach ($memberSavings as $saving)
                        <tr>
                          <td>{{ $locale->formatMoney($saving->amount, true) }}</td>
                          <td>{{ $saving->session->title }}</td>
                          <td>{{ $saving->duration }}</td>
                          <td>{{ $saving->distribution }} ({{ sprintf('%.2f', $distributionSum === 0 ?
                            0 : 100 * $saving->distribution / $distributionSum) }}%)</td>
                          <td>{{ $locale->formatMoney($saving->profit, true) }}</td>
                        </tr>
@endforeach
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
