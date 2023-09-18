@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $distributionSum = $fundings->sum('distribution');
@endphp
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>{!! __('meeting.labels.member') !!}</th>
                          <th>{!! __('meeting.labels.session') !!}</th>
                          <th>{!! __('meeting.labels.funding') !!}</th>
                          <th>{!! __('meeting.labels.duration') !!}</th>
                          <th>{!! __('meeting.labels.distribution') !!}</th>
                          <th>{!! __('meeting.labels.profit') !!}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($fundings as $funding)
                        <tr>
                          <td>{{ $funding->member->name }}</td>
                          <td>{{ $funding->session->title }}</td>
                          <td>{{ $locale->formatMoney($funding->amount, true) }}</td>
                          <td>{{ $funding->duration }}</td>
                          <td>{{ $funding->distribution }} / {{ $distributionSum }} - ({{ sprintf('%.2f',
                            $distributionSum === 0 ? 0 : 100 * $funding->distribution / $distributionSum) }}%)</td>
                          <td>{{ $locale->formatMoney($distributionSum === 0 ? 0 :
                            (int)($profitAmount * $funding->distribution / $distributionSum), true) }}</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
