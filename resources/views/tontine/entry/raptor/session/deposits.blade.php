@inject('locale', 'Siak\Tontine\Service\LocaleService')
                  <div class="section-title">
                    {{ __('meeting.titles.deposits') }}
                  </div>
@foreach ($pools as $pool)
@if ($session->enabled($pool))
@php
  $poolReceivables = $receivables->filter(fn($receivable) => $receivable->pool->id === $pool->id);
@endphp
@if ($poolReceivables->count() > 0)
                  <div class="table-title">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ $pool->title }}</th>
                          <th style="width:30%;">{{ __('common.labels.amount') }}: {{ $pool->deposit_fixed ?
                            $locale->formatMoney($pool->amount, true) : __('tontine.labels.types.libre') }}</th>
                        </tr>
                      </thead>
                    </table>
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr class="row">
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;">{{ __('common.labels.amount') }}</th>
                          <th style="width:5%;text-align:right;">{{ __('common.labels.paid') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($poolReceivables as $receivable)
                        <tr class="row">
                          <td>{{ $receivable->member->name }}</td>
                          <td style="text-align:right;">&nbsp;</td>
                          <td style="text-align:right;">{!! Form::checkbox('', '1', $receivable->paid) !!}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endif
@endforeach
