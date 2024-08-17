@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($charges->count() > 0)
                  <div class="section-title">{{ __('meeting.charge.titles.fees') }}</div>
@foreach($charges as $charge)
@php
  $chargeBills = $bills->filter(function($bill) use($charge) {
    return $bill->charge_id === $charge->id;
  });
@endphp
@if ($chargeBills->count() > 0)
                  <div class="table-title">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ $charge->name }}</th>
                          <th style="width:30%;">{{ __('common.labels.amount') }}: {{
                            $locale->formatMoney($charge->amount, true) }}</th>
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
@foreach ($chargeBills as $bill)
                        <tr class="row">
                          <td>{{ $bill->member->name }}</td>
                          <td style="text-align:right;">&nbsp;</td>
                          <td style="text-align:right;">{!! $htmlBuilder->checkbox('', $bill->paid, '1') !!}</td>
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
@endforeach
@endif
