@inject('locale', 'Siak\Tontine\Service\LocaleService')
@php
  $cash = 0;
@endphp
              <div class="row mt-0">
                <div class="col d-flex justify-content-center">
                  <h5>{{ __('figures.titles.amounts') }} ({{ $currency }})</h5>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead>
                    <tr>
                      <th></th>
                      <th class="report-round-cash-amount">{{ __('meeting.titles.auctions') }}</th>
                      <th class="report-round-cash-amount">{{ __('meeting.titles.fees') }}</th>
                      <th class="report-round-cash-amount">{{ __('meeting.titles.loans') }}</th>
                      <th class="report-round-cash-amount">{{ __('meeting.titles.refunds') }}</th>
                      <th class="report-round-cash-amount">{!! __('meeting.titles.savings') !!}</th>
                      <th class="report-round-cash-amount">{{ __('meeting.titles.disbursements') }}</th>
                      <th>{{ __('figures.titles.end') }}</th>
                    </tr>
                  </thead>
                  <tbody>
@foreach ($sessions as $session)
                    <tr>
                      <td>{{ $session->title }}</td>
@if($session->pending)
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
                      <td class="report-round-cash-amount"></td>
@else
@php
  $settlement = $settlements[$session->id] ?? 0;
  $auction = $auctions[$session->id] ?? 0;
  $saving = $savings[$session->id] ?? 0;
  $loan = $loans[$session->id] ?? 0;
  $refund = $refunds[$session->id] ?? 0;
  $disbursement = $disbursements[$session->id] ?? 0;
  $balance = $auction + $settlement + $refund + $saving - $loan - $disbursement;
  $cash += $balance;
@endphp
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($auction, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($settlement, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($loan, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($refund, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($saving, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($disbursement, false) !!}</b></td>
                      <td class="report-round-cash-amount"><b>{!! $locale->formatMoney($balance, false) !!}<br/>{!!
                        $locale->formatMoney($cash, false) !!}</b></td>
@endif
                    </tr>
@endforeach
                  </tbody>
                </table>
              </div>
