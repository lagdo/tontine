@inject('locale', 'Siak\Tontine\Service\LocaleService')
                    <table class="table table-bordered">
                      <thead>
                        <tr>
                          <th>&nbsp;</th>
                          <th class="currency">&nbsp;</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($members as $member)
@php
  $paid = $member->paid ?? 0;
  $due = $target->amount > $paid ? $target->amount - $paid : 0;
@endphp
                        <tr>
                          <td>{{ $member->name }}</td>
                          <td class="currency">{{ $locale->formatMoney($paid, true) }}@if ($due > 0)<br/>{{
                            __('meeting.target.labels.remaining', ['amount' => $locale->formatMoney($due, true)]) }}@endif</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                    <nav>{!! $pagination !!}</nav>
