@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $cash = 0;
@endphp
              <div class="section-title mt-0">{{ __('meeting.titles.amounts') }}</div>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th></th>
                      <th>{{ __('meeting.titles.fees') }}</th>
                      <th>{{ __('meeting.titles.refunds') }}</th>
                      <th>{{ __('meeting.titles.fundings') }}</th>
                      <th>{{ __('meeting.titles.loans') }}</th>
                      <th>{{ __('meeting.titles.disbursements') }}</th>
                      <th>{{ __('common.labels.total') }}</th>
                      <th>{{ __('figures.titles.end') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <th>{{ $session->title }}</th>
@if($session->pending)
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
                      <td></td>
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $funding = $fundings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $disbursement = $disbursements[$session->id] ?? 0;
  $balance = $settlement + $refund + $funding - $loan - $disbursement;
  $cash += $balance;
@endphp
                      <td class="currency"><b>{!! $locale->formatMoney($settlement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($refund, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($funding, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($loan, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($disbursement, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($balance, false) !!}</b></td>
                      <td class="currency"><b>{!! $locale->formatMoney($cash, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
