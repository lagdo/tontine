@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $distributionSum = $fundings->reduce(function($sum, $memberFundings) {
    return $sum + $memberFundings->sum('distribution');
  }, 0);
@endphp
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.funding') !!}</th>
                          <th>{!! __('meeting.labels.session') !!}</th>
                          <th>{!! __('meeting.labels.duration') !!}</th>
                          <th>{!! __('meeting.labels.distribution') !!}</th>
                          <th>{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($fundings as $memberFundings)
@if ($memberFundings->count() === 1)
@php
  $funding = $memberFundings[0];
@endphp
                        <tr>
                          <td>{{ $funding->member->name }}</td>
                          <td><b>{{ $locale->formatMoney($funding->amount, true) }}</b></td>
                          <td>{{ $funding->session->title }}</td>
                          <td>{{ $funding->duration }}</td>
                          <td><b>{{ $funding->distribution }} / {{ $distributionSum }} - ({{ sprintf('%.2f',
                            $distributionSum === 0 ? 0 : 100 * $funding->distribution / $distributionSum) }}%)</b></td>
                          <td><b>{{ $locale->formatMoney($funding->profit, true) }}</b></td>
                        </tr>
@else
@php
  $memberDistribution = $memberFundings->sum('distribution');
@endphp
                        <tr>
                          <td rowspan="{{ $memberFundings->count() + 1 }}">{{ $memberFundings[0]->member->name }}</td>
                          <td><b>{{ $locale->formatMoney($memberFundings->sum('amount'), true) }}</b></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><b>{{ $memberDistribution }} / {{ $distributionSum }} - ({{ sprintf('%.2f',
                            $distributionSum === 0 ? 0 : 100 * $memberDistribution / $distributionSum) }}%)</b></td>
                          <td><b>{{ $locale->formatMoney($memberFundings->sum('profit'), true) }}</b></td>
                        </tr>
@foreach ($memberFundings as $funding)
                        <tr>
                          <td>{{ $locale->formatMoney($funding->amount, true) }}</td>
                          <td>{{ $funding->session->title }}</td>
                          <td>{{ $funding->duration }}</td>
                          <td>{{ $funding->distribution }} / {{ $distributionSum }} - ({{ sprintf('%.2f',
                            $distributionSum === 0 ? 0 : 100 * $funding->distribution / $distributionSum) }}%)</td>
                          <td>{{ $locale->formatMoney($funding->profit, true) }}</td>
                        </tr>
@endforeach
@endif
@endforeach
                      </tbody>
                    </table>
