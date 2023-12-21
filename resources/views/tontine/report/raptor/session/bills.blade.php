@inject('locale', 'Siak\Tontine\Service\LocaleService')
@if ($charges['session']->count() > 0)
                  <div class="section-title">
                    {{ __('meeting.charge.titles.fees') }}
                  </div>
@foreach($charges['session'] as $charge)
@php
  $chargeBills = $bills->filter(function($bill) use($charge) {
    return $bill->charge_id === $charge->id;
  });
@endphp
@if ($chargeBills->count() > 0)
                  <div class="table-title">
                      {{ $charge->name }} :: {{ $charge->is_fixed ?
                        $locale->formatMoney($charge->amount, true) : ('(' . __('tontine.labels.types.libre') . ')') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('meeting.labels.member') }}</th>
                          <th style="width:15%;text-align:right;">{{ __('common.labels.paid') }}</th>
                          <th style="width:25%;text-align:right;">{{ __('common.labels.amount') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach ($chargeBills as $bill)
                        <tr>
                          <td>{{ $bill->member->name }}@if (($bill->session)) - {{ $bill->session->title }}@endif</td>
                          <td style="text-align:right;">{{ $bill->paid ? __('common.labels.yes') : __('common.labels.no') }}</td>
                          <td style="text-align:right;">{{ $bill->paid ? $locale->formatMoney($bill->amount, true) : '-' }}</td>
                        </tr>
@endforeach
                        <tr class="total">
                          <td>{{ __('common.labels.total') }}</td>
                          <td>{{ $charge->total_count }}</td>
                          <td>{{ $locale->formatMoney($charge->total_amount, true) }}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
@endforeach

                  <div class="table-title">
                    {{ __('tontine.report.titles.bills.session') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th style="text-align:right;" colspan="2">{{ __('tontine.report.titles.amounts.cashed') }}</th>
                          <th style="text-align:right;" colspan="2">{{ __('tontine.report.titles.amounts.disbursed') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($charges['session'] as $charge)
                        <tr>
                          <td>{{ $charge->name }}</td>
                          <td style="width:10%;text-align:right;">@if ($charge->total_count > 0){{ $charge->total_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($charge->total_count > 0){{
                            $locale->formatMoney($charge->total_amount, true) }}@else &nbsp; @endif</td>
                          <td style="width:10%;text-align:right;">@if ($charge->disbursement !== null){{
                            $charge->disbursement->total_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($charge->disbursement !== null){{
                            $locale->formatMoney($charge->disbursement->total_amount, true) }}@else &nbsp; @endif</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->

                  <div class="table-title">
                    {{ __('tontine.report.titles.bills.total') }}
                  </div>
                  <div class="table">
                    <table>
                      <thead>
                        <tr>
                          <th>{{ __('common.labels.title') }}</th>
                          <th style="text-align:right;" colspan="2">{{ __('tontine.report.titles.amounts.cashed') }}</th>
                          <th style="text-align:right;" colspan="2">{{ __('tontine.report.titles.amounts.disbursed') }}</th>
                        </tr>
                      </thead>
                      <tbody>
@foreach($charges['total'] as $charge)
                        <tr>
                          <td>{{ $charge->name }}</td>
                          <td style="width:10%;text-align:right;">@if ($charge->total_count > 0){{ $charge->total_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($charge->total_count > 0){{
                            $locale->formatMoney($charge->total_amount, true) }}@else &nbsp; @endif</td>
                          <td style="width:10%;text-align:right;">@if ($charge->disbursement !== null){{
                            $charge->disbursement->total_count }}@else &nbsp; @endif</td>
                          <td style="width:20%;text-align:right;">@if ($charge->disbursement !== null){{
                            $locale->formatMoney($charge->disbursement->total_amount, true) }}@else &nbsp; @endif</td>
                        </tr>
@endforeach
                      </tbody>
                    </table>
                  </div> <!-- End table -->
@endif
