                    <table class="table table-bordered responsive">
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
@foreach ($distribution->savings->groupBy('member_id') as $savings)
@if ($savings->count() === 1)
@php
  $saving = $savings[0];
@endphp
                        <tr>
                          <td>{{ $saving->member->name }}</td>
                          <td><b>{{ $locale->formatMoney($saving->amount) }}</b></td>
                          <td>{{ $saving->session->title }}</td>
                          <td>{{ $saving->duration }}</td>
                          <td><b>{{ $saving->parts }} ({{ sprintf('%.2f', $saving->percent) }}%)</b></td>
                          <td><b>{{ $locale->formatMoney($saving->profit) }}</b></td>
                        </tr>
@elseif ($savings->count() > 1)
                        <tr>
                          <td rowspan="{{ $savings->count() + 1 }}">{{ $savings[0]->member->name }}</td>
                          <td><b>{{ $locale->formatMoney($savings->sum('amount')) }}</b></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><b>{{ $savings->sum('parts') }} ({{ sprintf('%.2f', $savings->sum('percent')) }}%)</b></td>
                          <td><b>{{ $locale->formatMoney($savings->sum('profit')) }}</b></td>
                        </tr>
@foreach ($savings as $saving)
                        <tr>
                          <td>{{ $locale->formatMoney($saving->amount) }}</td>
                          <td>{{ $saving->session->title }}</td>
                          <td>{{ $saving->duration }}</td>
                          <td>{{ $saving->parts }} ({{ sprintf('%.2f', $saving->percent) }}%)</td>
                          <td>{{ $locale->formatMoney($saving->profit) }}</td>
                        </tr>
@endforeach
@endif
@endforeach
                      </tbody>
                    </table>
